<?php


namespace common\components;

use DateInterval;
use DatePeriod;

class ChartTools
{
    public static function getHourRange( $start = 0, $end = 86400, $step = 3600, $format = 'H:i:s' ) {
        $times = array();
        foreach ( range( $start, $end, $step ) as $timestamp ) {
            $hour_mins = gmdate( 'H:i', $timestamp );
            if ( ! empty( $format ) )
                $times[$hour_mins] = gmdate( $format, $timestamp );
            else $times[$hour_mins] = $hour_mins;
        }
        return $times;
    }

    public static function getHoursRange($first, $last, $step, $format) {
        $period = array();
        $current = strtotime( $first );
        $last = strtotime( $last );
        while( $current <= $last ) {
            $period[] = date( $format, $current );
            $current = strtotime( $step, $current );
        }
        return $period;
    }

    public static function getDaysRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
        $dates = array();
        $current = strtotime( $first );
        $last = strtotime( $last );
        while( $current <= $last ) {
            $dates[] = date( $format, $current );
            $current = strtotime( $step, $current );
        }
        return $dates;
    }

    public static function getMonthsRange($start, $end) {
        $startDate  = strtotime($start);
        $endDate    = strtotime($end);
        $firstMonth = date('Y-m', $startDate);
        $lastMonth  = date('Y-m', $endDate);
        $months = array($firstMonth);
        while($startDate < $endDate) {
            $startDate = strtotime(date('Y-m-d', $startDate).' +1 month');
            if(date('Y-m', $startDate) != $lastMonth && ($startDate < $endDate))
                $months[] = date('Y-m', $startDate);
        }
        if ($firstMonth != $lastMonth) {
            $months[] = date('Y-m', $endDate);
        }
        return $months;
    }

    public static function getWeeksRange($start, $end){
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
        foreach ($weeks as $week) {
            $firstEle = reset($week);
            $lastEle = end($week);
            array_push($weeksRanges, $firstEle .'/'.$lastEle);
        }
        return $weeksRanges;
    }
}