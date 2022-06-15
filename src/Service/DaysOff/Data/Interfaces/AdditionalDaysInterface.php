<?php

declare(strict_types = 1);
namespace App\Service\DaysOff\Data\Interfaces;

use Carbon\Carbon;

interface AdditionalDaysInterface
{
    /**
     * @param Carbon[] $days
     *
     * @return int
     */
    public function getAdditionalDays(array $days): int;
}