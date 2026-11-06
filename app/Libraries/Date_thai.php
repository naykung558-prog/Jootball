<?php

namespace App\Libraries;

class Date_thai
{
    private $monthname = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
    private $shortmonth = array("", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
    private $arabic_digit = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    private $th_digit = array('๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙');


    public function date_thai($value = '')
    {
        $strYear = date("Y", strtotime($value)) + 543;
        $strMonth = date("n", strtotime($value));
        $strDay = date("j", strtotime($value));
        $strMonthCut = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
        $strMonthThai = $strMonthCut[$strMonth];
        return "$strDay $strMonthThai $strYear";
    }
    public function date_thai2eng($date, $add = 0)
    {
        global $monthname, $shortmonth;
        if ($date != "") {
            $date = substr($date, 0, 10);
            $date = str_replace(array('-', '.'), '/', $date);
            list($day, $month, $year) = explode('/', $date);

            return ($year + $add) . "-" . $month . "-" . ($day);
        } else {
            return "";
        }
    }

    public function date_eng2thai($date, $add = 0, $dismonth = "L" /*รูปแบบเดือน */, $disyear = "L", $flag = ' ')
    {
        if ($date != "" && $date != '0000-00-00') {
            $date = substr($date, 0, 10);
            $date = str_replace(array('-', '.'), '/', $date);
            list($year, $month, $day) = explode('/', $date);
            if ($dismonth == "S") {
                $month = $this->shortmonth[$month * 1];
            } elseif ($dismonth == "L") {
                $month = $this->monthname[$month * 1];
            }
            $xyear = 0;
            if ($disyear == "S") {
                $xyear = substr(($year + $add), 2, 2);
            } else {
                $xyear = ($year + $add);
            }
            return ($day * 1) . "{$flag}" . $month . "{$flag}" . ($xyear);
        } else {
            return "";
        }
    }
    #เปลี่ยน รูปแบบวันที่
    #engthai = จาก eng to thai
    #thaieng = จาก thai to eng
    #thaidot = จาก eng to thai แบบแสดงเดือนเป็น ม.ค.
    public function dateFormat($value, $type)
    {
        if ($value == '' or $value == '0000-00-00') {
            $date = NULL;
        } else if ($type == "engthai") {

            $day = explode("-", $value);
            $date = $day[2] . '-' . $day[1] . '-' . ($day[0] + 543);
        } else if ($type == "thaieng") {

            $day = explode("-", $value);
            $date = ($day[2] - 543) . '-' . $day[1] . '-' . $day[0];
        } else if ($type == "engthai2") {

            $day = explode("-", $value);
            $date = $day[2] . '/' . $day[1] . '/' . ($day[0] + 543);
        } else if ($type == "thailinux") {

            $day = explode("/", $value);
            $date = ($day[2] - 543) . '-' . $day[1] . '-' . $day[0];
        } else if ($type == "thaidot") {

            $Month = array("", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
            $day = explode("-", $value);
            $Y = $day[0];
            $m = $day[1];
            if ($m < 10) {
                $m = str_replace("0", "", $m);
            }

            $d = $day[2];
            if ($d < 10) {
                $d = str_replace("0", "", $d);
            }
            $date = $d . ' ' . $Month[$m] . ' ' . ($Y + 543);
        } else if ($type == "thaifull") {
            $Month = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");

            $day = explode("-", $value);
            $Y = $day[0];
            $m = $day[1];
            if ($m < 10) {
                $m = str_replace("0", "", $m);
            }

            $d = $day[2];
            if ($d < 10) {
                $d = str_replace("0", "", $d);
            }
            $date = $d . ' ' . $Month[$m] . ' ' . ($Y + 543);
        } else if ($type == "inputtothai") {

            $Month = array("", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
            $day = explode("-", $value);
            $d = $day[0];
            $m = $day[1];
            if ($m < 10) {
                $m = str_replace("0", "", $m);
            }

            $Y = $day[2];
            if ($d < 10) {
                $d = str_replace("0", "", $d);
            }
            $date = $d . ' ' . $Month[$m] . ' ' . ($Y);
        } else if ($type == "thaitime") {
            $Month = array("", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
            $st = explode(" ", $value);
            $day = explode("-", $st[0]);
            $Y = $day[0] + 543;
            $m = $day[1];
            if ($m < 10) {
                $m = str_replace("0", "", $m);
            }

            $d = $day[2];
            if ($d < 10) {
                $d = str_replace("0", "", $d);
            }
            $date = $d . ' ' . $Month[$m] . ' ' . ($Y) . ' เวลา ' . $st[1];
        } else if ($type == "thaiengtime") {
            $day = explode("-", $value);
            $time = explode(' ', $day[2]);
            $timestamp = strtotime($time[1]);
            $formattedTime = date('H:i', $timestamp);
            $date = $time[0] . '/' . $day[1] . '/' . ($day[0] + 543) . ' ' . $formattedTime;
        } elseif ($type == "") {
            $date = explode("-", $value);
        }


        return $date;
    }
    function diff_day($date_start, $date_end)
    {
        $diff = date_diff(date_create($date_start), date_create($date_end));
        $y = $diff->format('%Y');
        $m = $diff->format('%m');
        $d = $diff->format('%d');
        $result = array('y' => $y, 'm' => $m, 'd' => $d);
        return $result;
    }
    function CalAge($age)
    {
        $currentDate = date('Y'); //

        $diff = abs(strtotime($currentDate) - strtotime($age));
        $yeardate = date("Y", strtotime($age));
        $result = $currentDate - $yeardate;
        // $result = array('y' => $years, 'm' => $months, 'd' => $days);
        return $result;
    }

    public function date_thaiDayMonYear($value = '')
    {
        $strYear = date("Y", strtotime($value)) + 543;
        $strMonth = date("n", strtotime($value));
        $strDay = date("j", strtotime($value));
        $strMonthCut = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
        $strMonthThai = $strMonthCut[$strMonth];
        return array($strDay, $strMonthThai, $strYear);
    }

    function diff_forday($date_start, $date_end)
    {
        $diff = date_diff(date_create($date_start), date_create($date_end));
        $d = $diff->format('%d');
        $result = $d;
        return $result;
    }
}
