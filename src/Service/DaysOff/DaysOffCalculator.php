<?php

declare(strict_types = 1);
namespace App\Service\DaysOff;

use App\Service\DaysOff\Algorithms\Factory\AlgorithmFactory;
use App\Service\DaysOff\Algorithms\Strategies\AbstractAlgorithm;
use App\Service\DaysOff\Data\Entity\DayOffDiff;
use App\Service\DaysOff\Data\Entity\WeekendDayOff;
use App\Service\DaysOff\Data\Factory\DaysOffProvidersFactory;
use App\Service\DaysOff\Data\Interfaces\AdditionalDaysInterface;
use App\Service\DaysOff\Data\Providers\AbstractDaysOffProvider;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class DaysOffCalculator
{
    public string  $algorithm;
    private int    $year;
    private int    $daysToSpend;
    private string $country;

    private AbstractDaysOffProvider $provider;
    public AbstractAlgorithm        $strategy;

    public function __construct(int $year, int $daysToSpend, string $country, string $algorithm)
    {
        $this->year        = $year;
        $this->daysToSpend = $daysToSpend;
        $this->country     = $country;
        $this->algorithm   = $algorithm;

        $this->loadProvider();
        $this->loadAlgorithm();
    }

    private function loadProvider(): void
    {
        $provider = DaysOffProvidersFactory::get($this->country);
        $provider->setYear($this->year);
        $this->provider = $provider;
    }

    private function loadAlgorithm(): void
    {
        $this->strategy = AlgorithmFactory::get($this->algorithm);
        $this->strategy->setDaysToSpend($this->daysToSpend);
    }

    final public function calculate()
    {
        $totalDays = $this->provider->getFreeDays();

        if ($this->provider instanceof AdditionalDaysInterface) {
            $this->daysToSpend += $this->provider->getAdditionalDays($totalDays);
        }

        $totalDays = $this->mergeWithWeekends($this->provider->filterOutDays($totalDays));
        $totalDays = $this->unifyTime($totalDays);

        sort($totalDays);

        $this->strategy->setNonWorkingDays($totalDays);

        $this->strategy->calculate();
    }

    private function mergeWithWeekends(array $totalDays): array
    {
        $date    = Carbon::create($this->year, CarbonInterface::JANUARY, 1, 12);
        $endDate = Carbon::create($this->year, CarbonInterface::DECEMBER, 31, 12);

        while ($date <= $endDate) {
            if (in_array($date->dayOfWeek, [CarbonInterface::SATURDAY, CarbonInterface::SUNDAY], true)) {
                $totalDays[] = WeekendDayOff::createFromTimestamp($date->getTimestamp());
            }
            $date->addDay();
        }

        return $totalDays;
    }

    private function unifyTime(array $totalDays)
    {
        /** @var Carbon $day */
        foreach ($totalDays as $day) {
            $day->setTime(12, 0);
        }
        return $totalDays;
    }

}