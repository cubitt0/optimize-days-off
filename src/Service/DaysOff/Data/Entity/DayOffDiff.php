<?php

declare(strict_types = 1);
namespace App\Service\DaysOff\Data\Entity;

use App\Service\DaysOff\Data\Traits\DayOffDiffTrait;
use Carbon\Carbon;

class DayOffDiff
{

    private Carbon $date;
    private ?int   $daysToPreviousNonWorkingDay = null;
    private ?int   $daysToNextNonWorkingDay     = null;

    /**
     * @return Carbon
     */
    public function getDate(): Carbon
    {
        return $this->date;
    }

    /**
     * @param Carbon $date
     *
     * @return DayOffDiff
     */
    public function setDate(Carbon $date): DayOffDiff
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDaysToNextNonWorkingDay(): ?int
    {
        return $this->daysToNextNonWorkingDay;
    }

    /**
     * @return int|null
     */
    public function getDaysToPreviousNonWorkingDay(): ?int
    {
        return $this->daysToPreviousNonWorkingDay;
    }

    /**
     * @param int|null $daysToPreviousNonWorkingDay
     *
     * @return DayOffDiff
     */
    public function setDaysToPreviousNonWorkingDay(?int $daysToPreviousNonWorkingDay): self
    {
        $this->daysToPreviousNonWorkingDay = $daysToPreviousNonWorkingDay;
        return $this;
    }

    /**
     * @param int|null $daysToNextNonWorkingDay
     *
     * @return DayOffDiff
     */
    public function setDaysToNextNonWorkingDay(?int $daysToNextNonWorkingDay): self
    {
        $this->daysToNextNonWorkingDay = $daysToNextNonWorkingDay;
        return $this;
    }
}