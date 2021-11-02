<?php

namespace App\Models\Traits;

use DateTime;
use DateTimeInterface;

/**
 *
 */
trait SerializeDateToIso8601
{
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(DateTime::ISO8601);
    }
}
