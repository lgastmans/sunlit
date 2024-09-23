<?php

use Carbon\Carbon;

function convertDateToMysql($date)
{
    return Carbon::parse($date)->toDateString();
}

/**
 * return the current Quarter
 */
function getCurrentQuarter()
{
    $res = 'Q1';

    $currentDate = new DateTime;
    $year = date('Y');

    $from = date($year.'-01-01');
    $to = date($year.'-03-31');
    $Q1Start = new DateTime($from);
    $Q1End = new DateTime($to);

    $from = date($year.'-04-01');
    $to = date($year.'-06-30');
    $Q2Start = new DateTime($from);
    $Q2End = new DateTime($to);

    $from = date($year.'-07-01');
    $to = date($year.'-09-30');
    $Q3Start = new DateTime($from);
    $Q3End = new DateTime($to);

    $from = date($year.'-10-01');
    $to = date($year.'-12-31');
    $Q4Start = new DateTime($from);
    $Q4End = new DateTime($to);

    if ($currentDate->getTimestamp() > $Q1Start->getTimestamp() &&
      $currentDate->getTimestamp() < $Q1End->getTimestamp()) {
        $res = 'Q1';
    } elseif ($currentDate->getTimestamp() > $Q2Start->getTimestamp() &&
      $currentDate->getTimestamp() < $Q2End->getTimestamp()) {
        $res = 'Q2';
    } elseif ($currentDate->getTimestamp() > $Q3Start->getTimestamp() &&
      $currentDate->getTimestamp() < $Q3End->getTimestamp()) {
        $res = 'Q3';
    } elseif ($currentDate->getTimestamp() > $Q4Start->getTimestamp() &&
      $currentDate->getTimestamp() < $Q4End->getTimestamp()) {
        $res = 'Q4';
    }

    return $res;
}

/**
 * use as follows: echo StaticArray::$month_short[1];
 */
class StaticArray
{
    public static $month_short = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec',
    ];

    public static $month_long = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
    ];
}
