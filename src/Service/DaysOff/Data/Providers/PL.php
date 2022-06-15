<?php
declare(strict_types = 1);
namespace App\Service\DaysOff\Data\Providers;

use App\Service\DaysOff\Data\Entity\PublicHoliday;
use App\Service\DaysOff\Data\Interfaces\AdditionalDaysInterface;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class PL extends AbstractDaysOffProvider implements AdditionalDaysInterface
{
    private array $easterValues = [
        '0-1582'    => [15, 6],
        '1583-1699' => [22, 2],
        '1700-1799' => [23, 3],
        '1800-1899' => [23, 4],
        '1900-2099' => [24, 5],
        '2100-9999' => [24, 6],
    ];

    final public function getStaticDates(): array
    {
        return [
            (new PublicHoliday())->setDate($this->year, CarbonInterface::JANUARY, 1),  //Nowy rok
            (new PublicHoliday())->setDate($this->year, CarbonInterface::JANUARY, 6),  //Trzech króli
            (new PublicHoliday())->setDate($this->year, CarbonInterface::MAY, 1),      //Święto Pracy
            (new PublicHoliday())->setDate($this->year, CarbonInterface::MAY, 3),      //Święto konstytucji
            (new PublicHoliday())->setDate($this->year, CarbonInterface::AUGUST, 15),  //Wniebowzięcie NMP
            (new PublicHoliday())->setDate($this->year, CarbonInterface::NOVEMBER, 1), //Wszystkich Świętych
            (new PublicHoliday())->setDate($this->year, CarbonInterface::NOVEMBER, 11),//Dzień niepodległości
            (new PublicHoliday())->setDate($this->year, CarbonInterface::DECEMBER, 25),//Boże narodzenie
            (new PublicHoliday())->setDate($this->year, CarbonInterface::DECEMBER, 26),//Drugi dzień ŚBN
        ];
    }

    final public function getDynamicDates(): array
    {
        return [
            $this->getEasterMondayDate(),
            $this->getFeastofCorpusChristiDate(),
        ];
    }

    final public function getAdditionalDays( array $days ): int
    {
        $additionalDays = 0;
        /** @var Carbon $day */
        foreach( $days as $day )
        {
            if( $day->dayOfWeek === CarbonInterface::SATURDAY )
            {
                $additionalDays++;
            }
        }
        return $additionalDays;
    }

    private function getEasterDate(): Carbon
    {
        $a = $this->getYear() % 19;
        $b = $this->getYear() % 4;
        $c = $this->getYear() % 7;

        [$k, $l] = $this->getEasterVariablesBasedOnYear();

        $d = (19 * $a + $k) % 30;
        $e = (2 * $b + 4 * $c + 6 * $d + $l) % 7;

        $easterDate = new Carbon();
        $easterDate->setDate($this->getYear(), 3, 22);
        $easterDate->addDays($d + $e);

        return $easterDate;
    }

    private function getEasterVariablesBasedOnYear(): array
    {
        foreach( $this->easterValues as $years => $values )
        {
            [$minYear, $maxYear] = explode('-',$years);
            if( $this->getYear() >= $minYear && $this->getYear() <= $maxYear )
            {
                return $values;
            }
        }
        return [];
    }

    private function getEasterMondayDate(): Carbon
    {
        return PublicHoliday::createFromTimestamp($this->getEasterDate()->clone()->addDay()->getTimestamp());
    }

    private function getFeastofCorpusChristiDate(): Carbon
    {
        return PublicHoliday::createFromTimestamp($this->getEasterDate()->clone()->addDays(60)->getTimestamp());
    }

    final public function getFreeDays(): array
    {
        return array_merge($this->getStaticDates(),$this->getDynamicDates());
    }
}