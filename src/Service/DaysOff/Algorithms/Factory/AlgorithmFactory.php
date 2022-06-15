<?php

declare(strict_types = 1);
namespace App\Service\DaysOff\Algorithms\Factory;

use App\Service\DaysOff\Algorithms\Enum\AlgorithmEnum;
use App\Service\DaysOff\Algorithms\Strategies\AbstractAlgorithm;
use App\Service\DaysOff\Algorithms\Strategies\LongestPeriod;
use App\Service\DaysOff\Algorithms\Strategies\NullAlgorithm;

class AlgorithmFactory
{
    public static function get(string $name): AbstractAlgorithm
    {
        switch ($name) {
            case AlgorithmEnum::LONGEST_PERIOD:
                return new LongestPeriod();
            default:
                return new NullAlgorithm();
        }
    }
}