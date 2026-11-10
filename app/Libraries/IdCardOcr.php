<?php

namespace App\Libraries;

/**
 * อ่านข้อมูลจากรูปบัตรประชาชนไทยด้วย Tesseract OCR
 *
 * ต้องติดตั้ง Tesseract พร้อมภาษา tha + eng บนเครื่องเซิร์ฟเวอร์
 * ตั้งตำแหน่งโปรแกรมได้ที่ .env: TESSERACT_CMD = 'C:\Program Files\Tesseract-OCR\tesseract.exe'
 *
 * ผลลัพธ์เป็นค่าที่ "เดา" จาก OCR ผู้ใช้ต้องตรวจทานก่อนบันทึกเสมอ
 */
class IdCardOcr
{
    private string $cmd;

    private const TH_PREFIXES = ['นางสาว', 'เด็กชาย', 'เด็กหญิง', 'ด.ช.', 'ด.ญ.', 'นาย', 'นาง', 'น.ส.'];

    private const EN_MONTHS = [
        'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6,
        'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12,
    ];

    private const TH_MONTHS = [
        'ม.ค.' => 1, 'ก.พ.' => 2, 'มี.ค.' => 3, 'เม.ย.' => 4, 'พ.ค.' => 5, 'มิ.ย.' => 6,
        'ก.ค.' => 7, 'ส.ค.' => 8, 'ก.ย.' => 9, 'ต.ค.' => 10, 'พ.ย.' => 11, 'ธ.ค.' => 12,
    ];

    public function __construct(?string $cmd = null)
    {
        $this->cmd = $cmd ?: (string) (env('TESSERACT_CMD') ?: 'tesseract');
    }

    /**
     * @return array{fields: array<string, string>, found: string[]}
     */
    public function read(string $imagePath): array
    {
        $prepared = $this->prepare($imagePath);
        try {
            // อ่าน 3 โหมด เพราะแต่ละโหมดเก่งคนละส่วน:
            // psm 3 (แบ่งหน้าอัตโนมัติ) อ่านชื่อภาษาไทยได้แม่นสุด, psm 11 / 6 เจอเลขบัตรผ่านลายโฮโลแกรมได้ดีกว่า
            $texts = [];
            foreach ([3, 11, 6] as $psm) {
                $texts[] = $this->tesseract($prepared, $psm);
            }
        } finally {
            if ($prepared !== $imagePath) {
                @unlink($prepared);
            }
        }

        $fields = $this->parse($texts);
        return ['fields' => $fields, 'found' => array_keys(array_filter($fields, 'strlen'))];
    }

    /** ปรับภาพเป็นขาวดำและขยาย 2 เท่า (ช่วยให้อ่านตัวเลขผ่านลายโฮโลแกรมได้ดีขึ้น) */
    private function prepare(string $path): string
    {
        if (!function_exists('imagecreatefromstring')) {
            return $path;
        }
        $img = @imagecreatefromstring((string) file_get_contents($path));
        if (!$img) {
            throw new \RuntimeException('อ่านไฟล์รูปไม่ได้');
        }
        $img = $this->fixOrientation($img, $path);

        $w = imagesx($img);
        $scale = $w < 2000 ? 2 : 1;
        if ($scale > 1) {
            $big = imagescale($img, $w * $scale, -1, IMG_BICUBIC);
            imagedestroy($img);
            $img = $big;
        }
        imagefilter($img, IMG_FILTER_GRAYSCALE);

        $out = WRITEPATH . 'cache/ocr_' . bin2hex(random_bytes(8)) . '.png';
        imagepng($img, $out);
        imagedestroy($img);
        return $out;
    }

    /** หมุนรูปจากมือถือตามข้อมูล EXIF */
    private function fixOrientation($img, string $path)
    {
        if (!function_exists('exif_read_data')) {
            return $img;
        }
        $exif = @exif_read_data($path);
        $angle = [3 => 180, 6 => -90, 8 => 90][$exif['Orientation'] ?? 1] ?? 0;
        if ($angle) {
            $rotated = imagerotate($img, $angle, 0);
            imagedestroy($img);
            return $rotated;
        }
        return $img;
    }

    private function tesseract(string $path, int $psm): string
    {
        $command = escapeshellarg($this->cmd) . ' ' . escapeshellarg($path) . ' stdout -l tha+eng --psm ' . $psm . ' 2>&1';
        exec($command, $lines, $code);
        if ($code !== 0) {
            throw new \RuntimeException('เรียก Tesseract ไม่สำเร็จ (ตรวจ TESSERACT_CMD ใน .env)');
        }
        return implode("\n", $lines);
    }

    /**
     * @param string[] $texts ผล OCR จากแต่ละโหมด เรียงตามความน่าเชื่อถือของชื่อภาษาไทย (ตัวแรกดีที่สุด)
     * @return array<string, string>
     */
    public function parse(array $texts): array
    {
        $text = implode("\n", $texts);
        $f = [
            'id_card'    => $this->findIdNumber($text),
            'prefix'     => '',
            'first_name' => '',
            'last_name'  => '',
            'first_name_en' => '',
            'last_name_en'  => '',
            'birth_date' => $this->findBirthDate($text),
            'sex'        => '',
        ];

        // ชื่อภาษาไทย: อ่านจากแต่ละโหมดแยกกัน แล้วเลือกชื่อที่หลายโหมดอ่านได้ตรงกัน
        // (ถ้าเสมอกัน ใช้โหมดที่มาก่อน) เพราะ OCR มักอ่านสระ/วรรณยุกต์เพี้ยนต่างกันไปในแต่ละโหมด
        $votes = [];
        foreach ($texts as $order => $t) {
            $name = $this->findThaiName($t);
            if ($name['prefix'] !== '' && $f['prefix'] === '') {
                $f['prefix'] = $name['prefix'];
            }
            if ($name['first'] === '') {
                continue;
            }
            $key = $name['first'] . ' ' . $name['last'];
            $votes[$key] ??= ['count' => 0, 'order' => $order, 'name' => $name];
            $votes[$key]['count']++;
        }
        if ($votes) {
            usort($votes, fn ($a, $b) => [$b['count'], $a['order']] <=> [$a['count'], $b['order']]);
            $f['first_name'] = $votes[0]['name']['first'];
            $f['last_name']  = $votes[0]['name']['last'];
        }

        // ชื่อภาษาอังกฤษ
        if (preg_match('/Name\s*[:.]?\s*(Mr|Mrs|Miss|Ms|Master)\.?\s+([A-Z][A-Za-z\-]+)/', $text, $m)) {
            $f['first_name_en'] = $m[2];
            $f['sex'] = in_array($m[1], ['Mr', 'Master'], true) ? 'M' : 'W';
        }
        if (preg_match('/Last\s*name\s*[:.]?\s*\R?\s*([A-Z][A-Za-z\-]+)/u', $text, $m)) {
            $f['last_name_en'] = $m[1];
        }

        if ($f['sex'] === '' && $f['prefix'] !== '') {
            $f['sex'] = in_array($f['prefix'], ['นาย', 'เด็กชาย', 'ด.ช.'], true) ? 'M' : 'W';
        }
        // ช่องชื่อ-นามสกุลในฟอร์มใช้ภาษาไทยเท่านั้น: ถ้าอ่านไทยไม่ได้ ปล่อยว่างให้ผู้ใช้กรอกเอง
        return $f;
    }

    /**
     * หาชื่อภาษาไทยจากบรรทัด "ชื่อตัวและชื่อสกุล ..." ในข้อความ OCR หนึ่งชุด
     * จับที่คำว่า "และชื่อ..." เพราะคำแรกของป้าย ("ชื่อตัว") มักถูกอ่านเพี้ยนเป็น รือ/ชือ/ร็อ
     *
     * @return array{prefix: string, first: string, last: string}
     */
    private function findThaiName(string $text): array
    {
        $out = ['prefix' => '', 'first' => '', 'last' => ''];
        foreach (preg_split('/\R/u', $text) as $line) {
            if (!preg_match('/และ\s*ช\S*/u', $line, $m, PREG_OFFSET_CAPTURE)) {
                continue;
            }
            $rest = substr($line, $m[0][1] + strlen($m[0][0]));
            preg_match_all('/[\x{0E00}-\x{0E7F}.]+/u', $rest, $mm);
            $words = [];
            foreach ($mm[0] as $w) {
                if (in_array($w, self::TH_PREFIXES, true)) {
                    $out['prefix'] = $w;
                    continue;
                }
                $w = trim($w, '.');
                if (mb_strlen($w) >= 2) {
                    $words[] = $w;
                }
            }
            if (count($words) >= 2) {
                // คำนำหน้าที่อ่านเพี้ยน (เช่น "ใย") จะอยู่หน้าสุด: ชื่อ-นามสกุลคือ 2 คำสุดท้าย
                $out['first'] = $words[count($words) - 2];
                $out['last']  = $words[count($words) - 1];
                return $out;
            }
        }
        return $out;
    }

    /** หาเลข 13 หลักที่ผ่าน checksum ของบัตรประชาชน; คืนรูปแบบ 0-0000-00000-00-0 */
    private function findIdNumber(string $text): string
    {
        preg_match_all('/\d[\d \-]{11,24}\d/', $text, $m);
        $fallback = '';
        foreach ($m[0] as $cand) {
            $d = preg_replace('/\D/', '', $cand);
            if (strlen($d) !== 13) {
                continue;
            }
            if (self::validIdChecksum($d)) {
                return self::formatId($d);
            }
            $fallback = $fallback ?: self::formatId($d);
        }
        return $fallback;
    }

    public static function validIdChecksum(string $d): bool
    {
        if (!preg_match('/^\d{13}$/', $d)) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $d[$i] * (13 - $i);
        }
        return (11 - $sum % 11) % 10 === (int) $d[12];
    }

    private static function formatId(string $d): string
    {
        return substr($d, 0, 1) . '-' . substr($d, 1, 4) . '-' . substr($d, 5, 5) . '-' . substr($d, 10, 2) . '-' . substr($d, 12, 1);
    }

    /** คืนวันเกิดเป็น d/m/Y (ค.ศ.) ตามรูปแบบช่อง birth_date ในฟอร์ม */
    private function findBirthDate(string $text): string
    {
        if (preg_match('/Birth\s*[:.]?\s*(\d{1,2})\s*([A-Za-z]{3})[a-z]*\.?,?\s*(\d{4})/', $text, $m)) {
            $mon = self::EN_MONTHS[strtolower($m[2])] ?? 0;
            if ($mon && checkdate($mon, (int) $m[1], (int) $m[3])) {
                return sprintf('%02d/%02d/%04d', $m[1], $mon, $m[3]);
            }
        }
        if (preg_match('/เกิดวันที่?\s*(\d{1,2})\s*([\x{0E00}-\x{0E7F}]\.?[\x{0E00}-\x{0E7F}]\.?)\s*(\d{4})/u', $text, $m)) {
            $key = rtrim($m[2], '.') . '.';
            $mon = self::TH_MONTHS[$key] ?? 0;
            $year = (int) $m[3] - 543;
            if ($mon && checkdate($mon, (int) $m[1], $year)) {
                return sprintf('%02d/%02d/%04d', $m[1], $mon, $year);
            }
        }
        return '';
    }
}
