<?php

namespace App;

use App\Service\DateRangeGeneratorInterface;

class CostCalculator
{
    const FLOAT_PRECISION = 10;
    const RAM_PER_STUDY = '0.5'; // 500MB / 1000 studies
    const RAM_COST_PER_DAY = '0.13272'; // 0.00553 USD * 24 hours (assumed that each study takes 24 hours to complete)
    const STORAGE_PER_STUDY = '10';
    const STORAGE_COST_PER_MB = '0.000097656'; // 0.10 USD / 1024 MB

    public DateRangeGeneratorInterface $dateRangeGenerator;

    public function __construct(DateRangeGeneratorInterface $generator)
    {
        $this->dateRangeGenerator = $generator;
    }

    /**
     * compute the ram and storage cost
     *
     * @param  mixed $studyCount
     * @param  mixed $studyIncreasePerMonth
     * @param  mixed $startMonth
     * @param  mixed $startYear
     * @param  mixed $monthsToGenerate
     * @return array
     */
    public function compute(
        int $studyCount,
        float $studyIncreasePerMonth,
        int $startMonth,
        int $startYear,
        int $monthsToGenerate
    ): array {
        $calculationData = $dates = $this->dateRangeGenerator->generate($startMonth, $startYear, $monthsToGenerate);
        $currentStudyCount = $studyCount;
        $increaseRate = bcdiv($studyIncreasePerMonth, '100', self::FLOAT_PRECISION);

        foreach ($dates as $key => $value) {
            if ($key > 0) {
                $currentStudyCount = bcadd(
                    $currentStudyCount,
                    bcmul($currentStudyCount, $increaseRate, self::FLOAT_PRECISION),
                    self::FLOAT_PRECISION
                );
            } else {
                $currentStudyCount = bcmul($currentStudyCount, $value['days'], self::FLOAT_PRECISION);
            }
    
            $ramUsage = bcmul($currentStudyCount, self::RAM_PER_STUDY, self::FLOAT_PRECISION);
            $ramCostPerDay = bcmul($ramUsage, self::RAM_COST_PER_DAY, self::FLOAT_PRECISION);
            $ramCostPerMonth = bcmul($ramCostPerDay, $value['days'], self::FLOAT_PRECISION);
            
            $storageUsage = bcmul($currentStudyCount, self::STORAGE_PER_STUDY, self::FLOAT_PRECISION);
            $storageCost = bcmul($storageUsage, self::STORAGE_COST_PER_MB, self::FLOAT_PRECISION);
            $storageCostPerMonth = bcmul($storageCost, $value['days'], self::FLOAT_PRECISION);
            $totalCost = round(bcadd($ramCostPerMonth, $storageCostPerMonth, self::FLOAT_PRECISION), 2);

            $formattedNumbers = [
                'numberStudies' => number_format(round($currentStudyCount, 2), 2),
                'totalCost' => '$ ' . number_format(round($totalCost, 2), 2)
            ];

            $calculationData[$key] = array_merge(
                $calculationData[$key],
                compact(
                    'currentStudyCount',
                    'ramUsage',
                    'ramCostPerDay',
                    'ramCostPerMonth',
                    'storageUsage',
                    'storageCost',
                    'storageCostPerMonth',
                    'totalCost',
                    'formattedNumbers'
                )
            );
        }
        return $calculationData;
    }
}
