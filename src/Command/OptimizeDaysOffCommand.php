<?php

namespace App\Command;

use App\Service\DaysOff\Algorithms\Enum\AlgorithmEnum;
use App\Service\DaysOff\DaysOffCalculator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OptimizeDaysOffCommand extends Command
{
    protected static $defaultName = 'app:optimize-days-off';
    protected static $defaultDescription = 'Calculates the best ';

    protected function configure()
    {
        $this->addArgument('days-off', InputArgument::REQUIRED, 'Your total amount of days off in specified year');
        $this->addArgument('year', InputArgument::OPTIONAL, 'Year to calcuate for', date('Y'));
        $this->addArgument('country', InputArgument::OPTIONAL, 'Country', 'pl');
        $this->addArgument('algorithm', InputArgument::OPTIONAL, 'Algorithm', AlgorithmEnum::LONGEST_PERIOD);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $calculator = new DaysOffCalculator(
            abs($input->getArgument('year')),
            $input->getArgument('days-off'),
            $input->getArgument('country'),
            $input->getArgument('algorithm')
        );
        $calculator->calculate();
        return self::SUCCESS;
    }
}
