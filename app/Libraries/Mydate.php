<?php
namespace App\Libraries;

class Mydate
{
    private $monthname = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
    private $shortmonth = array("", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
    private $arabic_digit = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    private $th_digit = array('๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙');
    
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

    public function date_eng2thaiShort($date, $add = 0, $dismonth = "L" /*รูปแบบเดือน */, $disyear = "L", $flag = ' ')
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
            return ($day * 1) . "{$flag}" . $month . "{$flag}" . ' พ.ศ. ' . ($xyear);
        } else {
            return "";
        }
    }

    public function date_thai2eng($date, $add = 0,$fomat = '')
    {
        global $monthname, $shortmonth;
        if ($date != "") {
            $date = substr($date, 0, 10);
            $date = str_replace(array('-', '.'), '/', $date);
            
            if($fomat == '/'){
                list($day, $month, $year) = explode('/', $date);
                return ($day) . "/" . $month . "/" . ($year + $add);
            }else{
                list($day, $month, $year) = explode('/', $date);
                return ($year + $add) . "-" . $month . "-" . ($day);
            }
        } else {
            return "";
        }
    }

    public function date_conv($date, $add = 0)
    {
        global $monthname, $shortmonth;
        if ($date != "") {
            $date = substr($date, 0, 10);
            $date = str_replace(array('-', '.'), '/', $date);
            
            list($year, $month, $day) = explode('/', $date);
            return ($day) . "/" . $month . "/" . ($year + $add);
        } else {
            return "";
        }
    }

    public function getMonthname($m = 0, $dismonth = "L")
    {
        if ($dismonth == "L") {
            return $this->monthname[$m];
        } else {
            return $this->shortmonth[$m];
        }
    }
    public function getMonthList()
    {
        return $this->monthname;
    }
    public function conv_th_digit($number)
    {

        return str_replace($this->arabic_digit, $this->th_digit, $number);
    }

    public function calculate_day($birthday, $today = "")
    {
        if ($birthday != "") {
            if ($today != "") {
                $today = new DateTime($today);
            } else {
                $today = new DateTime();
            }
            return $diff = $today->diff(new DateTime($birthday));

        }
    }
    public function expiredate($strdate, $days)
    { //หาจำนวนวันที่ โดยเพิ่มจำนวนวัน นับตั้งแต่ $strdate
        $time = strtotime($strdate) + (24 * 60 * 60 * $days); //แปลงวันที่ให้อยู่ในรูป timestamp
        $x = gmdate("Y-m-d", $time);
        return $x;
    }
    public function calculate_age($birthday)
    {
        if ($birthday != "") {
            $today = new DateTime();
            $diff = $today->diff(new DateTime($birthday));
            if ($diff->y) {
                return array('Y' => $diff->y, 'M' => $diff->m, 'D' => $diff->d);
            } elseif ($diff->m) {
                return array('Y' => 0, 'M' => $diff->m, 'D' => $diff->d);
            } else {
                return array('Y' => 0, 'M' => 0, 'D' => $diff->d);
            }
        }
    }

    public function createDateRangeArray($strDateFrom, $strDateTo)
    {
        // takes two dates formatted as YYYY-MM-DD and creates an
        // inclusive array of the dates between the from and to dates.

        // could test validity of dates here but I'm already doing
        // that in the main script

        $aryRange = array();

        $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

        if ($iDateTo >= $iDateFrom) {
            array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
            while ($iDateFrom < $iDateTo) {
                $iDateFrom += 86400; // add 24 hours
                array_push($aryRange, date('Y-m-d', $iDateFrom));
            }
        }
        return $aryRange;
    }

    public function getWorkingDays($startDate, $endDate, $holidays = array())
    {
        // do strtotime calculations just once
        $endDate = strtotime($endDate);
        $startDate = strtotime($startDate);

        //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
        //We add one to inlude both dates in the interval.
        $days = ($endDate - $startDate) / 86400 + 1;

        $no_full_weeks = floor($days / 7);
        $no_remaining_days = fmod($days, 7);

        //It will return 1 if it's Monday,.. ,7 for Sunday
        $the_first_day_of_week = date("N", $startDate);
        $the_last_day_of_week = date("N", $endDate);

        //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
        //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
        if ($the_first_day_of_week <= $the_last_day_of_week) {
            if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) {
                $no_remaining_days--;
            }

            if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) {
                $no_remaining_days--;
            }

        } else {
            // (edit by Tokes to fix an edge case where the start day was a Sunday
            // and the end day was NOT a Saturday)

            // the day of the week for start is later than the day of the week for end
            if ($the_first_day_of_week == 7) {
                // if the start date is a Sunday, then we definitely subtract 1 day
                $no_remaining_days--;

                if ($the_last_day_of_week == 6) {
                    // if the end date is a Saturday, then we subtract another day
                    $no_remaining_days--;
                }
            } else {
                // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
                // so we skip an entire weekend and subtract 2 days
                $no_remaining_days -= 2;
            }
        }

        //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
        //---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
        $workingDays = $no_full_weeks * 5;
        if ($no_remaining_days > 0) {
            $workingDays += $no_remaining_days;
        }

        //We subtract the holidays
        foreach ($holidays as $holiday) {
            $time_stamp = strtotime($holiday);
            //If the holiday doesn't fall in weekend
            if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N", $time_stamp) != 6 && date("N", $time_stamp) != 7) {
                $workingDays--;
            }

        }

        return $workingDays;
    }

}