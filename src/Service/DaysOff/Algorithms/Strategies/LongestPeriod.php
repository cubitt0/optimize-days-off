<?php

declare(strict_types = 1);
namespace App\Service\DaysOff\Algorithms\Strategies;

use App\Service\DaysOff\Algorithms\Interfaces\AlgorithmInterface;
use App\Service\DaysOff\Data\Entity\CalculatedDayOff;
use App\Service\DaysOff\Data\Entity\DayOffDiff;
use App\Service\DaysOff\Data\Entity\DaysOffPeriod;
use App\Service\DaysOff\Data\Entity\PublicHoliday;
use App\Service\DaysOff\Data\Entity\WeekendDayOff;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class LongestPeriod extends AbstractAlgorithm
{
    /** @var DayOffDiff[] */
    public array $daysOffDiff;

    /** @var Carbon[]  */
    private array $daysOff = [];

    public function calculate(): void
    {
        $this->createDiffObjects($this->getNonWorkingDays());
        $this->calculateDaysOff();

        $periods = $this->loadPeriods();
        $this->handleDaysOffBasedOnPeriods($periods);

        $periods = $this->loadPeriods();
        foreach ($this->daysOff as $index => $key)
        {
            $this->daysOff[$key->getTimestamp()] = $key;
            unset($this->daysOff[$index]);
        }

        dd(
            array_map(static function (DaysOffPeriod $el) {
            return $el->getStartDate()->format('d-m-Y').' -> '.
                $el->getEndDate()->format('d-m-Y').': '.$el->getTotal();
        },$periods),
            array_map(static function (Carbon $el) {
                return $el->format('d-m-Y');
            },$this->daysOff),
        );

    }

    public function getDaysOff(): array
    {
        return $this->daysOff;
    }

    private function createDiffObjects(array $totalDays): void
    {
        $this->daysOffDiff = [];

        $total = count(array_filter($totalDays, static function ($el) { return !$el instanceof WeekendDayOff; }));

        /** @var Carbon $day */
        foreach ($totalDays as $index => $day) {
            if ($day instanceof WeekendDayOff) {
                continue;
            }

            $dayOffDiff = new DayOffDiff;
            $dayOffDiff->setDate($day);

            if ($index !== 0) {
                $previousNonWorkingDay = $totalDays[$index - 1];
                $dayOffDiff->setDaysToPreviousNonWorkingDay($previousNonWorkingDay->diffInDays($day));
            }

            if ($index !== $total - 1) {
                $nextNonWorkingDay = $totalDays[$index + 1];
                $dayOffDiff->setDaysToNextNonWorkingDay($day->diffInDays($nextNonWorkingDay));
            }

            $this->daysOffDiff[] = $dayOffDiff;
        }
    }

    private function addDayOffIfNotExists(Carbon $dayOff): void
    {
        if (isset($this->daysOff[$dayOff->getTimestamp()])) {
            return;
        }
        $this->daysOff[$dayOff->getTimestamp()] = CalculatedDayOff::createFromTimestamp($dayOff->getTimestamp());
    }

    private function calculateDaysOff(): void
    {
        foreach ($this->daysOffDiff as $dayOffDiff) {
            if ($dayOffDiff->getDaysToNextNonWorkingDay() === 2) {
                $this->addDayOffIfNotExists($dayOffDiff->getDate()->clone()->addDay());
            }

            if ($dayOffDiff->getDaysToPreviousNonWorkingDay() === 2) {
                $this->addDayOffIfNotExists($dayOffDiff->getDate()->clone()->subDay());
            }
        }
    }

    /**
     * @param Carbon[] $days
     */
    private function calculateLongestPeriods(array $days)
    {
        $periods = [];
        $total   = count($days);
        foreach ($days as $index => $day) {
            if (empty($startDate)) {
                $startDate = $day;
            }

            if ($index !== $total - 1) {
                $nextDay = $days[$index + 1];
                if ($day->diffIndays($nextDay) > 1) {
                    $period = new DaysOffPeriod;
                    $period->setStartDate($startDate);
                    $period->setEndDate($day);
                    $period->setTotal($startDate->diffInDays($day) + 1);

                    $periods[] = $period;
                    $startDate = null;
                }
            } else {
                $period = new DaysOffPeriod;
                $period->setStartDate($startDate);
                $period->setEndDate($day);
                $period->setTotal($startDate->diffInDays($day) + 1);

                $periods[] = $period;
                $startDate = null;
            }
        }

        usort($periods, function (DaysOffPeriod $a, DaysOffPeriod $b) {
            $result = $b->getTotal() <=> $a->getTotal();

            if ($result === 0) {
                return $a->getStartDate() <=> $b->getStartDate();
            }

            return $result;
        });

        return $periods;
    }


    private function handleDaysOffBasedOnPeriods(array $periods): void
    {
        $totalDaysSpent = count($this->daysOff);
        $leftToSpend    = $this->getDaysToSpend() - $totalDaysSpent;

        if ($leftToSpend === 0) {
            return;
        }

        if ($leftToSpend < 0) {
            $this->removeDaysOff(abs($leftToSpend) - 1);
            return;
        }

        $this->addDaysOff($periods, $leftToSpend);
    }

    private function removeDaysOff(int $toRemove): void
    {
        for ($i = 0; $i <= $toRemove; $i++) {
            array_pop($this->daysOff);
        }
    }

    /**
     * @param DaysOffPeriod[] $periods
     * @param int             $leftToSpend
     *
     * @return void
     */
    private function addDaysOff(array $periods, int $leftToSpend)
    {
        $periods = array_values(array_filter($periods, static function(DaysOffPeriod $el)
        {
            return $el->getTotal() > 2;
        }));

        $periodsCount = count($periods);

        for ($i = 0; $i < $leftToSpend; $i++) {

            $diffToPrevious = 365;
            $diffToNext = 365;

            $periodIndex = $i % $periodsCount;

            $currentPeriod = $periods[$periodIndex];

            if ($periodIndex !== 0) {
                $previousDay    = $periods[$periodIndex - 1]->getEndDate();
                $diffToPrevious = $currentPeriod->getStartDate()->diffInDays($previousDay);
            }

            if ($periodIndex !== $periodsCount - 1) {
                $nextDay        = $periods[$periodIndex + 1]->getStartDate();
                $diffToNext = $currentPeriod->getEndDate()->diffInDays($nextDay);
            }

            if($diffToPrevious < $diffToNext)
            {
                $date = $currentPeriod->getStartDate()->clone()->subDay();
                $periods[$periodIndex]->setStartDate($date);
            }
            else
            {
                $date = $currentPeriod->getEndDate()->clone()->addDay();
                $periods[$periodIndex]->setEndDate($date);
            }
            $this->daysOff[] = CalculatedDayOff::createFromTimestamp($date->getTimestamp());
        }

        sort($this->daysOff);
    }

    private function loadPeriods():array
    {
        $days = array_merge($this->getNonWorkingDays(), array_values($this->getDaysOff()));
        sort($days);
        return $this->calculateLongestPeriods($days);
    }
}