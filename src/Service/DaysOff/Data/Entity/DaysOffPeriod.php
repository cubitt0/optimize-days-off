<?php

declare(strict_types = 1);
namespace App\Service\DaysOff\Data\Entity;

use Carbon\Carbon;

class DaysOffPeriod
{
    private Carbon $startDate;

    /**
     * @return Carbon
     */
    public function getStartDate(): Carbon
    {
        return $this->startDate;
    }

    /**
     * @param Carbon $startDate
     *
     * @return DaysOffPeriod
     */
    public function setStartDate(Carbon $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getEndDate(): Carbon
    {
        return $this->endDate;
    }

    /**
     * @param Carbon $endDate
     *
     * @return DaysOffPeriod
     */
    public function setEndDate(Carbon $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal(int $total): self
    {
        $this->total = $total;
        return $this;
    }
    private Carbon $endDate;
    private int    $total;

}