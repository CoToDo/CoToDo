<?php

namespace App\Model;
class DayEnum {
    const MONDAY = 0;
    const TUESDAY = 1;
    const WEDNESDAY = 2;
    const THURSDAY = 3;
    const FRIDAY = 4;
    const SATURDAY = 5;
    const SUNDAY = 6;

    public static $values = array(
        self::MONDAY => "Mon",
        self::TUESDAY => "Tue",
        self::WEDNESDAY => "Wed",
        self::THURSDAY => "Thu",
        self::FRIDAY => "Fri",
        self::SATURDAY => "Sat",
        self::SUNDAY => "Sun"
    );


}