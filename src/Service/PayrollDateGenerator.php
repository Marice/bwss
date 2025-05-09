<?php

namespace App\Service;

class PayrollDateGenerator
{
    /**
     * @return array<int, array<int, string>>
     */
    public function generate(
        int $startMonth,
        int $year
    ): array
    {
        $csvData = [["Month", "Base Salary Payment Date", "Bonus Payment Date"]];

        for ($month = $startMonth; $month <= 12; $month++) {
            $baseDate = new \DateTimeImmutable("$year-$month-01");
            $payDay = $baseDate->modify('last day of this month');

            if (in_array($payDay->format('N'), ['6', '7'])) { // check if payday is in the weekend
                $payDay = $payDay->modify('last Friday');
            }

            $bonusDate = new \DateTimeImmutable("$year-$month-15");

            if (in_array($bonusDate->format('N'), ['6', '7'])) { // check day of week
                // Find first Wednesday after the 15th
                while ($bonusDate->format('N') !== '3') {
                    $bonusDate = $bonusDate->modify('+1 day');
                }
            }

            $csvData[] = [
                $baseDate->format('F'),
                $payDay->format('jS \o\f F Y'),
                $bonusDate->format('jS \o\f F Y'),
            ];
        }

        return $csvData;
    }
}