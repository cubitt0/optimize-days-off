<?php

declare(strict_types = 1);
namespace App\Service\DaysOff\Data\Interfaces;

use Carbon\Carbon;
use DateTime;

interface DaysOffProviderInterface
{
    /**
     * @return Carbon[]
     */
    public function getFreeDays(): array;
}