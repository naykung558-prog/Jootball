<?php

namespace App\Libraries;

class ProdCloneService
{
    const PROD_DB = 'jootball';
    const DEV_DB  = 'jootball_dev';

    const PROD_UPLOADS = '/var/www/html/jootball.com/public/uploads/';
    const DEV_UPLOADS  = '/var/www/html/dev.jigsawinnovation.com/jootball/public/uploads/';

    /** Allowed dev project roots — clone จะรันได้เฉพาะเมื่อ codebase อยู่ใน path เหล่านี้เท่านั้น */
    const ALLOWED_DEV_ROOTS = [
        '/var/www/html/dev.jigsawinnovation.com/jootball',
        '/Applications/MAMP/htdocs/jootball',
    ];

    const EXCLUDE_TABLES = [
        'admin_remember_tokens',
        'password_reset_tokens',
        'coach_invitations',
        'tournament_duplicate_check_link',
        'tournament_source_check_link',
        'temp_tour_match',
    ];

    private string $host;
    private string $user;
    private string $pass;

    public function __construct()
    {
        $this->host = env('database.default.hostname', '');
        $this->user = env('database.default.username', '');
        $this->pass = env('database.default.password', '');
    }

    public function cloneAll(): array
    {
        $this->assertSafeEnvironment();

        @set_time_limit(600);

        $started  = microtime(true);
        $dbResult = $this->cloneDatabase();

        // DB clone fail → ไม่ sync ไฟล์ทับ dev ที่พังครึ่ง ๆ
        if (! $dbResult['success']) {
            return [
                'success'      => false,
                'elapsed_sec'  => round(microtime(true) - $started, 2),
                'database'     => $dbResult,
                'files'        => null,
                'excluded'     => self::EXCLUDE_TABLES,
            ];
        }

        $filesResult = $this->cloneFiles();
        $elapsed = round(microtime(true) - $started, 2);

        return [
            'success'      => $dbResult['success'] && $filesResult['success'],
            'elapsed_sec'  => $elapsed,
            'database'     => $dbResult,
            'files'        => $filesResult,
            'excluded'     => self::EXCLUDE_TABLES,
        ];
    }

    /**
     * SAFETY GUARDS — ป้องกันการ clone ทับ prod โดยเด็ดขาด
     * ถ้า guard ตัวใดตัวหนึ่ง fail → throw exception ทันที (defense in depth)
     */
    public function assertSafeEnvironment(): void
    {
        // Guard 1: Project root path บนดิสก์ต้องอยู่ใน ALLOWED_DEV_ROOTS เท่านั้น
        // (filesystem-based — โกหกผ่าน .env ไม่ได้)
        $root     = defined('ROOTPATH') ? ROOTPATH : (realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR);
        $rootReal = realpath(rtrim($root, '/\\')) ?: rtrim($root, '/\\');
        $rootNorm = rtrim(str_replace('\\', '/', $rootReal), '/');

        $matched = false;
        foreach (self::ALLOWED_DEV_ROOTS as $allowed) {
            if ($rootNorm === rtrim($allowed, '/')) {
                $matched = true;
                break;
            }
        }
        if (!$matched) {
            throw new \RuntimeException(sprintf(
                'SAFETY ABORT: project path "%s" is not in ALLOWED_DEV_ROOTS. Allowed: [%s]',
                $rootNorm,
                implode(', ', self::ALLOWED_DEV_ROOTS)
            ));
        }

        // Guard 2: database.default ใน .env ต้องเป็น DEV_DB เท่านั้น
        $currentDb = env('database.default.database', '');
        if ($currentDb !== self::DEV_DB) {
            throw new \RuntimeException(sprintf(
                'SAFETY ABORT: current DB connection is "%s", expected "%s". Will NEVER run if .env points to anything other than dev DB.',
                $currentDb,
                self::DEV_DB
            ));
        }

        // Guard 3: HTTP_HOST ต้องไม่ใช่ prod domain (jootball.com without dev.)
        $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
        if ($host !== '') {
            $isProdDomain = (strpos($host, 'jootball.com') !== false) && (strpos($host, 'dev.') === false);
            if ($isProdDomain) {
                throw new \RuntimeException('SAFETY ABORT: HTTP_HOST="' . $host . '" looks like prod domain.');
            }
        }

        // Guard 4: PROD_DB และ DEV_DB ต้องไม่เหมือนกัน
        if (self::PROD_DB === self::DEV_DB) {
            throw new \RuntimeException('SAFETY ABORT: PROD_DB and DEV_DB constants are identical.');
        }

        // Guard 5: PROD_UPLOADS และ DEV_UPLOADS ต้องไม่ใช่ path เดียวกัน
        $prodReal = realpath(self::PROD_UPLOADS) ?: rtrim(self::PROD_UPLOADS, '/');
        $devReal  = realpath(self::DEV_UPLOADS)  ?: rtrim(self::DEV_UPLOADS, '/');
        if ($prodReal === $devReal) {
            throw new \RuntimeException('SAFETY ABORT: PROD_UPLOADS and DEV_UPLOADS resolve to the same path: ' . $prodReal);
        }
    }

    /**
     * Run guards without throwing — for UI to show status.
     * Returns array of failed guard messages (empty array if all pass).
     */
    public function checkEnvironment(): array
    {
        try {
            $this->assertSafeEnvironment();
            return [];
        } catch (\Throwable $e) {
            return [$e->getMessage()];
        }
    }

    private function cloneDatabase(): array
    {
        $started = microtime(true);

        $cnfFile    = $this->writeMyCnf();
        $tmpBase    = sys_get_temp_dir() . '/jootball_clone_' . time() . '_' . bin2hex(random_bytes(4));
        $structFile = $tmpBase . '_struct.sql';
        $dataFile   = $tmpBase . '_data.sql';
        $structErr  = $tmpBase . '_struct.err';
        $dataErr    = $tmpBase . '_data.err';

        try {
            // 1) structure ของ "ทุกตาราง" (รวม EXCLUDE_TABLES — ให้ตารางมีอยู่แต่ว่าง) + routines/triggers
            //    stderr แยกออกไฟล์ .err ไม่ปนลง .sql (mysqldump warning จะทำ import พังที่บรรทัด 1)
            $structCmd = sprintf(
                'mysqldump --defaults-extra-file=%s --no-data --routines --triggers --no-tablespaces --set-gtid-purged=OFF %s > %s 2>%s',
                escapeshellarg($cnfFile),
                escapeshellarg(self::PROD_DB),
                escapeshellarg($structFile),
                escapeshellarg($structErr)
            );
            exec($structCmd, $structOut, $structRc);
            if ($structRc !== 0 || !file_exists($structFile) || filesize($structFile) === 0) {
                return ['success' => false, 'step' => 'mysqldump structure', 'rc' => $structRc, 'output' => $this->tailFile($structErr)];
            }

            // 2) data ของทุกตาราง "ยกเว้น" EXCLUDE_TABLES (token/temp — ไม่ copy ข้อมูล sensitive)
            $ignoreFlags = '';
            foreach (self::EXCLUDE_TABLES as $t) {
                $ignoreFlags .= ' --ignore-table=' . escapeshellarg(self::PROD_DB . '.' . $t);
            }
            $dataCmd = sprintf(
                'mysqldump --defaults-extra-file=%s --no-create-info --single-transaction --quick --no-tablespaces --set-gtid-purged=OFF %s %s > %s 2>%s',
                escapeshellarg($cnfFile),
                $ignoreFlags,
                escapeshellarg(self::PROD_DB),
                escapeshellarg($dataFile),
                escapeshellarg($dataErr)
            );
            exec($dataCmd, $dataOut, $dataRc);
            // data dump มี preamble (SET ...) เสมอ → filesize=0 = พังจริง
            if ($dataRc !== 0 || !file_exists($dataFile) || filesize($dataFile) === 0) {
                return ['success' => false, 'step' => 'mysqldump data', 'rc' => $dataRc, 'output' => $this->tailFile($dataErr)];
            }

            $dumpSize = filesize($structFile) + filesize($dataFile);

            // 3) Pristine reset — drop+recreate dev ให้เริ่มจากศูนย์ (กันตาราง dev-only ค้าง:
            //    logs_player_merge, player_id_card_backup, zz_del_* + charset ตรง prod)
            $resetResult = $this->resetDevDatabase($cnfFile);
            if (!$resetResult['success']) {
                return $resetResult;
            }

            // 4) import structure → ทุกตารางถูกสร้าง (EXCLUDE_TABLES มีอยู่แต่ว่าง)
            $impStructCmd = sprintf(
                'mysql --defaults-extra-file=%s %s < %s 2>&1',
                escapeshellarg($cnfFile),
                escapeshellarg(self::DEV_DB),
                escapeshellarg($structFile)
            );
            exec($impStructCmd, $isOut, $isRc);
            if ($isRc !== 0) {
                return ['success' => false, 'step' => 'import structure', 'rc' => $isRc, 'output' => implode("\n", $isOut)];
            }

            // 5) import data (FK off กัน insert order ละเมิด FK, แล้ว reset กลับ 1)
            $impDataCmd = sprintf(
                '{ echo %s; cat %s; echo %s; } | mysql --defaults-extra-file=%s %s 2>&1',
                escapeshellarg('SET FOREIGN_KEY_CHECKS=0;'),
                escapeshellarg($dataFile),
                escapeshellarg('SET FOREIGN_KEY_CHECKS=1;'),
                escapeshellarg($cnfFile),
                escapeshellarg(self::DEV_DB)
            );
            exec($impDataCmd, $idOut, $idRc);
            if ($idRc !== 0) {
                return ['success' => false, 'step' => 'import data', 'rc' => $idRc, 'output' => implode("\n", $idOut)];
            }

            return [
                'success'      => true,
                'dump_bytes'   => $dumpSize,
                'dump_human'   => $this->humanBytes($dumpSize),
                'reset'        => $resetResult,
                'elapsed_sec'  => round(microtime(true) - $started, 2),
            ];
        } finally {
            foreach ([$structFile, $dataFile, $structErr, $dataErr, $cnfFile] as $f) {
                if (file_exists($f)) { @unlink($f); }
            }
        }
    }

    /**
     * Drop + recreate DEV_DB ให้สะอาดก่อน import dump ของ prod
     * charset/collation ดึงจาก prod เพื่อให้ตรงกัน
     * Defense in depth: hard-guard ห้าม drop ถ้าไม่ใช่ DEV_DB เด็ดขาด
     */
    private function resetDevDatabase(string $cnfFile): array
    {
        if (self::DEV_DB === '' || self::DEV_DB === self::PROD_DB) {
            return ['success' => false, 'step' => 'reset guard', 'error' => 'invalid DEV_DB (empty or == PROD_DB)'];
        }
        // Runtime guard ซ้ำ (จุดที่ DROP DATABASE จริง) — .env ต้องชี้ dev เท่านั้น
        // ไม่พึ่ง assertSafeEnvironment() อย่างเดียว เผื่อ method ถูกเรียกนอก cloneAll()
        if (env('database.default.database', '') !== self::DEV_DB) {
            return ['success' => false, 'step' => 'reset guard', 'error' => 'current .env DB != DEV_DB — refuse to DROP'];
        }

        // ดึง charset/collation ของ prod (information_schema = trusted source)
        $charset   = 'utf8mb4';
        $collation = 'utf8mb4_general_ci';
        $metaCmd = sprintf(
            'mysql --defaults-extra-file=%s -N -B -e %s 2>&1',
            escapeshellarg($cnfFile),
            escapeshellarg(sprintf(
                "SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME='%s'",
                self::PROD_DB
            ))
        );
        $metaOut = [];
        exec($metaCmd, $metaOut, $metaRc);
        if ($metaRc === 0 && !empty($metaOut[0])) {
            $parts = preg_split('/\s+/', trim($metaOut[0]));
            // identifier เท่านั้น — กัน meta แปลกปลอม inject เข้า DDL (CREATE DATABASE)
            if (!empty($parts[0]) && preg_match('/^[A-Za-z0-9_]+$/', $parts[0])) { $charset   = $parts[0]; }
            if (!empty($parts[1]) && preg_match('/^[A-Za-z0-9_]+$/', $parts[1])) { $collation = $parts[1]; }
        }

        $sql = sprintf(
            'DROP DATABASE IF EXISTS `%s`; CREATE DATABASE `%s` CHARACTER SET %s COLLATE %s;',
            self::DEV_DB,
            self::DEV_DB,
            $charset,
            $collation
        );
        $resetCmd = sprintf(
            'mysql --defaults-extra-file=%s -e %s 2>&1',
            escapeshellarg($cnfFile),
            escapeshellarg($sql)
        );
        $out = [];
        exec($resetCmd, $out, $rc);
        if ($rc !== 0) {
            return ['success' => false, 'step' => 'reset dev db', 'rc' => $rc, 'output' => implode("\n", $out)];
        }

        return ['success' => true, 'charset' => $charset, 'collation' => $collation];
    }

    private function cloneFiles(): array
    {
        $started = microtime(true);

        if (!is_dir(self::PROD_UPLOADS)) {
            return ['success' => false, 'step' => 'check prod path', 'error' => 'PROD_UPLOADS not found: ' . self::PROD_UPLOADS];
        }
        if (!is_dir(self::DEV_UPLOADS)) {
            if (!@mkdir(self::DEV_UPLOADS, 0775, true)) {
                return ['success' => false, 'step' => 'mkdir dev', 'error' => 'cannot create DEV_UPLOADS'];
            }
        }

        // -rL = recursive + transform symlinks
        // ไม่ใช้ -a เพราะ web user ไม่ได้เป็นเจ้าของ existing files/dirs ใน dev
        // → utimensat()/chown() ส่ง EPERM (Operation not permitted)
        $cmd = sprintf(
            'rsync -rL --delete %s %s 2>&1',
            escapeshellarg(rtrim(self::PROD_UPLOADS, '/') . '/'),
            escapeshellarg(rtrim(self::DEV_UPLOADS, '/') . '/')
        );

        exec($cmd, $out, $rc);
        if ($rc !== 0) {
            return [
                'success' => false,
                'step'    => 'rsync',
                'rc'      => $rc,
                'output'  => implode("\n", array_slice($out, -20)),
            ];
        }

        $sizeOut = [];
        exec('du -sb ' . escapeshellarg(self::DEV_UPLOADS) . ' 2>/dev/null | awk \'{print $1}\'', $sizeOut);
        $bytes = (int) ($sizeOut[0] ?? 0);

        return [
            'success'     => true,
            'size_bytes'  => $bytes,
            'size_human'  => $this->humanBytes($bytes),
            'elapsed_sec' => round(microtime(true) - $started, 2),
        ];
    }

    private function writeMyCnf(): string
    {
        $file = sys_get_temp_dir() . '/jootball_mycnf_' . bin2hex(random_bytes(8));
        // strip CR/LF/tab จาก host/user — กัน .env เพี้ยน inject บรรทัดทับใน ini
        $host = preg_replace('/[\r\n\t]/', '', $this->host);
        $user = preg_replace('/[\r\n\t]/', '', $this->user);
        $pass = str_replace(["\r", "\n"], '', $this->pass);
        $body = "[client]\nhost=" . $host . "\nuser=" . $user . "\npassword=\"" . str_replace('"', '\\"', $pass) . "\"\n";
        file_put_contents($file, $body);
        @chmod($file, 0600);
        return $file;
    }

    /** อ่านท้ายไฟล์ (เช่น stderr ของ mysqldump) สำหรับ surface error — จำกัดความยาว */
    private function tailFile(string $path, int $max = 2000): string
    {
        if (!is_file($path)) {
            return '';
        }
        $content = (string) file_get_contents($path);
        return strlen($content) > $max ? substr($content, -$max) : $content;
    }

    private function humanBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        $val = (float) $bytes;
        while ($val >= 1024 && $i < count($units) - 1) {
            $val /= 1024;
            $i++;
        }
        return number_format($val, 2) . ' ' . $units[$i];
    }
}
