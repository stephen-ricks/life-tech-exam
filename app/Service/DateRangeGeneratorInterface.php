<?php

namespace App\Service;

interface DateRangeGeneratorInterface
{
    public function generate(int $startMonth, int $startYear, int $monthsToGenerate = 1): array;
}
