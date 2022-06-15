<?php

declare(strict_types = 1);
namespace App\Service\DaysOff\Algorithms\Interfaces;

use Carbon\Carbon;

interface AlgorithmInterface
{

    public function calculate(): void;

    /**
     * @return Carbon[]
     */
    public function getDaysOff(): array;
}