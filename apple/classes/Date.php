<?php

namespace Barter;

use DateTime;
use DateTimeZone;

class Date
{
    public static function now(): string
    {
        return (new DateTime('now', new DateTimeZone('+3 UTC')))
            ->format('Y-m-d H:i:s');
    }

    public static function parse(string $date)
    {
        return (new DateTime($date))->format('Y-m-d');
    }

    public static function firstDayOfThisMonth(): string
    {
        return (new DateTime('first day of this month', new DateTimeZone('+3 UTC')))->format('Y-m-d 00:00:00');
    }

    public static function lastDayOfThisMonth(): string
    {
        return (new DateTime('last day of this month', new DateTimeZone('+3 UTC')))->format('Y-m-d 23:59:59');
    }

    public static function firstDayOfPreviousMonth(): string
    {
        return (new DateTime('first day of previous month', new DateTimeZone('+3 UTC')))->format('Y-m-d 00:00:00');
    }

    public static function lastDayOfPreviousMonth(): string
    {
        return (new DateTime('last day of previous month', new DateTimeZone('+3 UTC')))->format('Y-m-d 23:59:59');
    }
}
