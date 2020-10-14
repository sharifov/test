<?php


namespace common\components;

use DateInterval;
use DatePeriod;

class ChartTools
{
    /**
     * @param int $start
     * @param int $end
     * @param int $step
     * @param string $format
     * @return array
     */
    public static function getHourRange($start = 0, $end = 86400, $step = 3600, $format = 'H:i')
    {
        $times = array();
        foreach (range($start, $end, $step) as $timestamp) {
            $hour_mins = gmdate('H:i', $timestamp);
            if (! empty($format)) {
                $times[$hour_mins] = gmdate($format, $timestamp);
            } else {
                $times[$hour_mins] = $hour_mins;
            }
        }
        return $times;
    }

    public static function getHoursRange($first, $last, $step, $format)
    {
        $period = array();
        $current = strtotime($first);
        $last = strtotime($last);
        while ($current <= $last) {
            $period[] = date($format, $current);
            $current = strtotime($step, $current);
        }
        return $period;
    }

    public static function getDaysRange($first, $last, $step = '+1 day', $format = 'Y-m-d')
    {
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);
        while ($current <= $last) {
            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }
        return $dates;
    }

    public static function getMonthsRange($start, $end, $format = 'Y-m')
    {
        $startDate  = strtotime($start);
        $endDate    = strtotime($end);
        $firstMonth = date($format, $startDate);
        $lastMonth  = date($format, $endDate);
        $months = array($firstMonth);
        while ($startDate < $endDate) {
            $startDate = strtotime(date('Y-m-d', $startDate).' +1 month');
            if (date($format, $startDate) != $lastMonth && ($startDate < $endDate)) {
                $months[] = date($format, $startDate);
            }
        }
        if ($firstMonth != $lastMonth) {
            $months[] = date($format, $endDate);
        }
        return $months;
    }

    public static function getWeeksRange($start, $end)
    {
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($start, $interval, $end);
        $weekNumber = 1;
        $weeks = array();
        foreach ($dateRange as $date) {
            $weeks[$weekNumber][] = $date->format('Y-m-d');
            if ($date->format('w') == 0) {
                $weekNumber++;
            }
        }
        $weeksRanges = [];
        foreach ($weeks as $key => $week) {
            if ($key == 1) {
                $firstEle = $start->modify('last monday')->format('Y-m-d');
            } else {
                $firstEle = reset($week);
            }

            $lastEle = end($week);
            array_push($weeksRanges, $firstEle .'/'.$lastEle);
        }
        return $weeksRanges;
    }

    public static function getWeek($periodName)
    {
        $week = [];
        $startWeek = strtotime("last monday -12", strtotime($periodName));
        $endWeek = strtotime("next sunday midnight -36", $startWeek);
        $week['start'] = $startWeek;
        $week['end'] = $endWeek;

        return $week;
    }

    public static function getCurrentMonth()
    {
        $month = [];
        $month['start'] = strtotime('first day of this month noon');
        $month['end'] = strtotime('last day of this month noon -24');
        return $month;
    }

    public static function getWeekDaysRange()
    {
        return ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    }

    /* $start_week = strtotime("last monday noon", strtotime("today"));
        $end_week = strtotime("next sunday", strtotime("today"));
        $start = date("Y-m-d H:i",$start_week);
        $end = date("Y-m-d",$end_week);
        $currentWeek = $start.' to '.$end;


        $previous_week = strtotime("-1 week");
        $start_week = strtotime("last monday noon",$previous_week);
        $end_week = strtotime("next sunday",$start_week);
        $start_week = date("Y-m-d H:i",$start_week);
        $end_week = date("Y-m-d",$end_week);
        $lastWeek =  $start_week.' to '.$end_week ;

        $currentMonthStart = date('Y-m-01');
        $currentMonthEnd  = date('Y-m-t');
        $currentMonth = $currentMonthStart.' to '.$currentMonthEnd ;
     */
}
