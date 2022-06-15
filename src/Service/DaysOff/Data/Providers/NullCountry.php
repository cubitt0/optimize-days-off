<?php

declare(strict_types = 1);
namespace App\Service\DaysOff\Data\Providers;

use Carbon\Carbon;

class NullCountry extends AbstractDaysOffProvider
{
    public function getFreeDays(): array
    {
        return [];
    }
}