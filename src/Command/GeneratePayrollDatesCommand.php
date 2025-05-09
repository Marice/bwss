<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\PayrollDateGenerator;

#[AsCommand(
    name: 'app:generate-payroll-dates',
    description: 'Generates payroll dates for the rest of the current year and writes them to a CSV file',
)]
class GeneratePayrollDatesCommand extends Command
{
    public function __construct(
        private readonly PayrollDateGenerator $generator
    )
    {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int
    {
        $io = new SymfonyStyle($input, $output);
        $year = (int)(new \DateTime())->format('Y');
        $month = (int)(new \DateTime())->format('n');

        $csvData = $this->generator->generate($month, $year);

        $filename = 'payroll_dates.csv';
        $fp = fopen($filename, 'w');

        // Check if file was successfully opened
        if ($fp === false) {
            $io->error("Failed to open file '$filename' for writing.");
            return Command::FAILURE;
        }

        foreach ($csvData as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
        $io->success("CSV file '$filename' generated successfully.");

        return Command::SUCCESS;
    }
}