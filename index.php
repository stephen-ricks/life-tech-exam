<?php

use App\CostCalculator;
use App\Service\DateRange;

require __DIR__."/vendor/autoload.php";

try {
    $request = $_SERVER['REQUEST_URI'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    if ($request === '/' && $requestMethod === 'GET') {
        echo file_get_contents('resources/home/index.html');
    } elseif ($request === '/calculate' && $requestMethod === 'POST') {
        $dailyStudyCount = $_REQUEST['studyCount'];
        $percentageIncrease = $_REQUEST['percentage'];
        $startMonth = $_REQUEST['month'];
        $startYear = $_REQUEST['year'];
        $numberOfMonthsToForecast = $_REQUEST['forecastMonthCount'];

        if (empty($dailyStudyCount) ||
            empty($percentageIncrease) ||
            empty($startMonth) ||
            empty($startYear) ||
            empty($numberOfMonthsToForecast)
        ) {
            throw new Exception('Please check input parameters');
        }

        $calculator = new CostCalculator((new DateRange));
        $calculationResult = $calculator->compute(
            $dailyStudyCount,
            $percentageIncrease,
            $startMonth,
            $startYear,
            $numberOfMonthsToForecast
        );

        header('Content-type: application/json');
        echo json_encode(['data' => $calculationResult]);
    } else {
        http_response_code(404);
        echo "Not found";
    }
} catch (Throwable $error) {
    http_response_code(406);
    echo $error->getMessage();
}
