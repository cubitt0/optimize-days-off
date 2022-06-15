<?php
declare(strict_types = 1);
namespace App\Service\DaysOff\Data\Providers;

use App\Service\DaysOff\Data\Interfaces\DaysOffProviderInterface;
use Carbon\Carbon;
use Carbon\CarbonInterface;

abstract class AbstractDaysOffProvider implements DaysOffProviderInterface
{
    protected int $year;

    public function __construct( int $year = null )
    {
        $this->year = $year ? : (int)date('Y');
    }

    final public function filterOutDays( array $days ): array
    {
        /** @var Carbon $day */
        foreach( $days as $key => $day )
        {

            if( in_array($day->dayOfWeek, [CarbonInterface::SATURDAY, CarbonInterface::SUNDAY], true) )
            {
                unset($days[$key]);
            }
        }
        return array_values($days);
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     *
     * @return AbstractDaysOffProvider
     */
    public function setYear( int $year ): self
    {
        $this->year = $year;
        return $this;
    }
}