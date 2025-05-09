<?php

namespace App\Tests\Service;

use App\Service\PayrollDateGenerator;
use PHPUnit\Framework\TestCase;

class PayrollDateGeneratorTest extends TestCase
{
    public function testGeneratesCorrectDataStructure(): void
    {
        $data = (new PayrollDateGenerator())->generate(1, 2025); // From Jan to Dec
        $this->assertCount(13, $data); // 1 header row + 12 months

        foreach ($data as $i => $row) {
            $this->assertCount(3, $row);

            if ($i > 0) {
                $this->assertMatchesRegularExpression('/^[A-Za-z]+$/', $row[0]); // Month
                $this->assertMatchesRegularExpression('/^\d{1,2}(st|nd|rd|th) of [A-Za-z]+ \d{4}$/', $row[1]); // Salary
                $this->assertMatchesRegularExpression('/^\d{1,2}(st|nd|rd|th) of [A-Za-z]+ \d{4}$/', $row[2]); // Bonus
            }
        }
    }

    public function testBonusDateForWeekendFallsOnWednesday(): void
    {
        $data = (new PayrollDateGenerator())->generate(6, 2025); // June 2025 — 15th is a Sunday

        $juneRow = $data[1]; // 0 = header, 1 = June
        $bonusDate = $juneRow[2];

        $this->assertEquals('18th of June 2025', $bonusDate); // First Wednesday after 15th
    }

    public function testBaseSalaryOnWeekendMovesToFriday(): void
    {
        $data = (new PayrollDateGenerator())->generate(8, 2025); // August 2025 — 31st is Sunday

        $augustRow = $data[1];
        $salaryDate = $augustRow[1];

        $this->assertEquals('29th of August 2025', $salaryDate); // Friday
    }
}
