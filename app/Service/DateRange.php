<?php

namespace App\Service;

use DateTime;

class DateRange implements DateRangeGeneratorInterface
{
    public function generate(int $startMonth, int $startYear, int $monthsToGenerate = 1): array
    {
        $dates = [];
        $startDate = date('M Y', mktime(0, 0, 0, $startMonth, 1, $startYear));
        $currentDate = new DateTime($startDate);

        for ($counter = 0; $counter < $monthsToGenerate; $counter++) {
            array_push($dates, [
                'date' => $currentDate->format("M Y"),
                'dateObject' => clone $currentDate,
                'days' => cal_days_in_month(CAL_GREGORIAN, $currentDate->format('m'), $currentDate->format('Y')),
            ]);

            $currentDate = $currentDate->modify('next month');
        }

        return $dates;
    }
}
