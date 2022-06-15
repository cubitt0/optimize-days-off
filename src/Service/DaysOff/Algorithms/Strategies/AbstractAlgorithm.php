<?php

declare(strict_types = 1);
namespace App\Service\DaysOff\Algorithms\Strategies;

use App\Service\DaysOff\Algorithms\Interfaces\AlgorithmInterface;
use Carbon\Carbon;

abstract class AbstractAlgorithm implements AlgorithmInterface
{
    /** @var Carbon[]  */
    private array $nonWorkingDays = [];

    private int $daysToSpend;

    /**
     * @return int
     */
    public function getDaysToSpend(): int
    {
        return $this->daysToSpend;
    }

    /**
     * @return Carbon[]
     */
    public function getNonWorkingDays(): array
    {
        return $this->nonWorkingDays;
    }

    /**
     * @param Carbon[] $freeDays
     */
    public function setNonWorkingDays(array $freeDays): self
    {
        $this->nonWorkingDays = $freeDays;
        return $this;
    }

    /**
     * @param int $daysToSpend
     */
    public function setDaysToSpend(int $daysToSpend): self
    {
        $this->daysToSpend = $daysToSpend;
        return $this;
    }
}