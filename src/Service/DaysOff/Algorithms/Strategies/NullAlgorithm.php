<?php

declare(strict_types = 1);
namespace App\Service\DaysOff\Algorithms\Strategies;

use Carbon\Carbon;

class NullAlgorithm extends AbstractAlgorithm
{

    public function calculate(): void
    {
        foreach ($this->getNonWorkingDays() as $day)
        {

        }
    }

    public function getDaysOff(): array
    {
        return [];
    }
}