<?php

namespace OpenBoleto;


class Data
{

    public static function _dateToDays($year, $month, $day)
    {
        $century = substr($year, 0, 2);
        $year = substr($year, 2, 2);
        if ($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            if ($year) {
                $year--;
            } else {
                $year = 99;
                $century--;
            }
        }
        return (floor((146097 * $century) / 4) +
            floor((1461 * $year) / 4) +
            floor((153 * $month + 2) / 5) +
            $day + 1721119);
    }
} 