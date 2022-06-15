<?php
declare(strict_types = 1);
namespace App\Service\DaysOff\Data\Factory;

use App\Service\DaysOff\Data\Interfaces\DaysOffProviderInterface;
use App\Service\DaysOff\Data\Providers\AbstractDaysOffProvider;
use App\Service\DaysOff\Data\Providers\NullCountry;
use App\Service\DaysOff\Data\Providers\PL;

class DaysOffProvidersFactory
{
    public static function get( string $name = 'pl' ): AbstractDaysOffProvider
    {
        switch( strtolower($name) )
        {
            case 'pl':
                return new PL;
            default:
                return new NullCountry;
        }
    }
}