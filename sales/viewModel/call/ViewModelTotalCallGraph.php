<?php

namespace sales\viewModel\call;

use common\components\ChartTools;
use DateInterval;
use DatePeriod;
use DateTime;
use sales\entities\call\CallGraphsSearch;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class ViewModelTotalCallGraph
 * @package sales\viewmodel\call
 *
 * @property string $totalCallsGraphData
 * @property string $totalCallsGraphDataAvg
 * @property string $totalCallsRecDurationData
 * @property string $totalCallsRecDurationDataAVG
 * @property array $callData
 * @property CallGraphsSearch $callGraphsSearch
 * @property SqlDataProvider $dataProvider
 * @property array $gridColumns
 * @property array $exportData
 */
class ViewModelTotalCallGraph
{
    public $totalCallsGraphData;

    public $totalCallsGraphDataAvg;

    public $totalCallsRecDurationData;

    public $totalCallsRecDurationDataAVG;

    public $callGraphsSearch;

    public $callData;

    public $dataProvider;

    public $gridColumns;

    public $exportData;

    /**
     * ViewModelTotalCallGraph constructor.
     * @param SqlDataProvider $callData
     * @param CallGraphsSearch $callGraphsSearch
     * @throws \Exception
     */
    public function __construct(
        SqlDataProvider $callData,
        CallGraphsSearch $callGraphsSearch
    ) {
        $this->callData = $callData->getModels();
        $this->callGraphsSearch = $callGraphsSearch;

        //$this->calcTotalCallsAverage($this->callData);

        $this->formatTotalCallsGraphData();
        $this->formatTotalCallsGraphDataAvg();
        $this->totalCallsRecDurationData();
        $this->totalCallsRecDurationDataAVG();

        $this->prepareStatsExport();

        $this->dataProvider = (new ArrayDataProvider(['allModels' => $this->exportData]));
        $this->gridColumns = $this->fetchGridColumns();
    }

    private function prepareStatsExport():void
    {
        $data = [];
        $finalData = [];
        $mappedData = [];

        foreach ($this->callData as $rowIndex) {
            $data['incoming'] = 0;
            $data['incomingAvg'] = 0;
            $data['incomingTotal'] = 0;
            $data['incomingAvgDuration'] = 0;

            $data['outgoing'] = 0;
            $data['outgoingAvg'] = 0;
            $data['outgoingTotal'] = 0;
            $data['outgoingAvgDuration'] = 0;

            $data['total'] = 0;
            $data['totalCallsDuration'] = 0;
            $data['totalAvgDuration'] = 0;

            foreach ($this->callData as $row) {
                if ($rowIndex['group'] == $row['group']) {
                    $data['date'] = $row['group'];
                    if ($row['callType'] === 'in') {
                        $data['incoming'] = $row['totalCalls'];
                        $data['incomingAvg'] = $row['avgCallsPerGroup'];
                        $data['incomingTotal'] = $row['totalCallsDuration'];
                        $data['incomingAvgDuration'] = $row['avgCallDuration'];
                    }

                    if ($row['callType'] === 'out') {
                        $data['outgoing'] = $row['totalCalls'];
                        $data['outgoingAvg'] = $row['avgCallsPerGroup'];
                        $data['outgoingTotal'] = $row['totalCallsDuration'];
                        $data['outgoingAvgDuration'] = $row['avgCallDuration'];
                    }

                    if ($row['callType'] === 'total') {
                        $data['total'] = $row['totalCalls'];
                        $data['totalCallsDuration'] = $row['totalCallsDuration'];
                        $data['totalAvgDuration'] = $row['avgCallDuration'];
                    }
                    $finalData[$row['group']] = $data;
                }
            }
        }

        foreach ($finalData as $arr) {
            array_push($mappedData, [
                'date' => $arr['date'],
                'incoming' => (int)$arr['incoming'],
                'outgoing' => (int)$arr['outgoing'],
                'total' => (int)$arr['total'],
                'incomingAvg' => (int)$arr['incomingAvg'],
                'outgoingAvg' => (int)$arr['outgoingAvg'],
                'incomingTotal' => (int)$arr['incomingTotal'],
                'outgoingTotal' => (int)$arr['outgoingTotal'],
                'totalCallsDuration' => (int)$arr['totalCallsDuration'],
                'incomingAvgDuration' => (int)$arr['incomingAvgDuration'],
                'outgoingAvgDuration' =>  (int)$arr['outgoingAvgDuration'],
                'totalAvgDuration' => (int)$arr['totalAvgDuration'],
            ]);
        }

        if ($this->callGraphsSearch->callGraphGroupBy === CallGraphsSearch::DATE_FORMAT_WEEKDAYS) {
            $mappedData = $this->setWeekDayName($mappedData);
        }

        if ($this->callGraphsSearch->callGraphGroupBy === CallGraphsSearch::DATE_FORMAT_MONTH) {
            $mappedData = $this->setMonthName($mappedData);
        }

        $this->exportData = $mappedData;
    }

    /**
     * @return void
     */
    private function formatTotalCallsGraphData(): void
    {
        $data = [];
        $finalData = [];
        $mappedData = [];

        foreach ($this->callData as $rowIndex) {
            $data['incoming'] = 0;
            $data['outgoing'] = 0;
            foreach ($this->callData as $row) {
                if ($rowIndex['group'] == $row['group']) {
                    $data['date'] = $row['group'];

                    if ($row['callType'] === 'in') {
                        $data['incoming'] = $row['totalCalls'];
                    }

                    if ($row['callType'] === 'out') {
                        $data['outgoing'] = $row['totalCalls'];
                    }

                    /*if ($row['callType'] === 'total'){
                        $data['total'] = $row['totalCalls'];
                    }*/
                    $finalData[$row['group']] = $data;
                }
            }
        }

        foreach ($finalData as $arr) {
            array_push($mappedData, [
                $arr['date'],
                (int)$arr['incoming'],
                'Incoming Calls: ' . (int)$arr['incoming'],
                (int)$arr['outgoing'],
                'Outgoing Calls: ' . (int)$arr['outgoing'],
                ]);
        }

        $mappedData = $this->normalizeAxisX($mappedData);

        $this->totalCallsGraphData = json_encode(ArrayHelper::merge([[
            'Date',
            'Incoming',
            [
                'type' => 'string',
                'role' => 'tooltip'
            ],
            'Outgoing',
            [
                'type' => 'string',
                'role' => 'tooltip'
            ],
        ]], $mappedData));
    }

    /**
     * @return void
     */
    private function formatTotalCallsGraphDataAvg(): void
    {
        $data = [];
        $finalData = [];
        $mappedData = [];

        foreach ($this->callData as $rowIndex) {
            $data['incoming'] = 0;
            $data['outgoing'] = 0;
            foreach ($this->callData as $row) {
                if ($rowIndex['group'] == $row['group']) {
                    $data['date'] = $row['group'];
                    if ($row['callType'] === 'in') {
                        $data['incoming'] = $row['avgCallsPerGroup'];
                    }

                    if ($row['callType'] === 'out') {
                        $data['outgoing'] = $row['avgCallsPerGroup'];
                    }

                    /*if ($row['callType'] === 'total'){
                        $data['total'] = $row['avgCallsPerGroup'];
                    }*/
                    $finalData[$row['group']] = $data;
                }
            }
        }

        foreach ($finalData as $arr) {
            array_push($mappedData, [
                $arr['date'],
                (int)$arr['incoming'],
                'Incoming Calls AVG: ' . (int)$arr['incoming'],
                (int)$arr['outgoing'],
                'Outgoing Calls AVG: ' . (int)$arr['outgoing'],
            ]);
        }

        $mappedData = $this->normalizeAxisX($mappedData);

        $this->totalCallsGraphDataAvg = json_encode(ArrayHelper::merge([[
            'Date',
            'Incoming',
            [
                'type' => 'string',
                'role' => 'tooltip'
            ],
            'Outgoing',
            [
                'type' => 'string',
                'role' => 'tooltip'
            ],
        ]], $mappedData));
    }

    /**
     * @return void
     */
    private function totalCallsRecDurationData(): void
    {
        $data = [];
        $finalData = [];
        $mappedData = [];

        foreach ($this->callData as $rowIndex) {
            $data['incoming'] = 0;
            $data['outgoing'] = 0;
            foreach ($this->callData as $row) {
                if ($rowIndex['group'] == $row['group']) {
                    $data['date'] = $row['group'];
                    if ($row['callType'] === 'in') {
                        $data['incoming'] = $row['totalCallsDuration'];
                    }

                    if ($row['callType'] === 'out') {
                        $data['outgoing'] = $row['totalCallsDuration'];
                    }

                    /*if ($row['callType'] === 'total'){
                        $data['total'] = $row['totalCallsDuration'];
                    }*/
                    $finalData[$row['group']] = $data;
                }
            }
        }

        foreach ($finalData as $arr) {
            array_push($mappedData, [
                $arr['date'],
                (int)$arr['incoming'],
                'Incoming Call Duration: ' . Yii::$app->formatter->asDuration((int)$arr['incoming']),
                (int)$arr['outgoing'],
                'Outgoing Call Duration: ' . Yii::$app->formatter->asDuration((int)$arr['outgoing']),
            ]);
        }

        $mappedData = $this->normalizeAxisX($mappedData);

        $this->totalCallsRecDurationData = json_encode(ArrayHelper::merge([[
            'Date',
            'Incoming Call Duration',
            [
                'type' => 'string',
                'role' => 'tooltip'
            ],
            'Outgoing Call Duration',
            [
                'type' => 'string',
                'role' => 'tooltip'
            ],
        ]], $mappedData));
    }

    /**
     * @return void
     */
    private function totalCallsRecDurationDataAVG(): void
    {
        $data = [];
        $finalData = [];
        $mappedData = [];

        foreach ($this->callData as $rowIndex) {
            $data['incoming'] = 0;
            $data['outgoing'] = 0;
            foreach ($this->callData as $row) {
                if ($rowIndex['group'] == $row['group']) {
                    $data['date'] = $row['group'];
                    if ($row['callType'] === 'in') {
                        $data['incoming'] = $row['avgCallDuration'];
                    }

                    if ($row['callType'] === 'out') {
                        $data['outgoing'] = $row['avgCallDuration'];
                    }

                    /*if ($row['callType'] === 'total'){
                        $data['total'] = $row['avgCallDuration'];
                    }*/
                    $finalData[$row['group']] = $data;
                }
            }
        }

        foreach ($finalData as $arr) {
            array_push($mappedData, [
                $arr['date'],
                (int)$arr['incoming'],
                'Incoming Call Duration AVG: ' . Yii::$app->formatter->asDuration((int)$arr['incoming']),
                (int)$arr['outgoing'],
                'Outgoing Call Duration AVG: ' . Yii::$app->formatter->asDuration((int)$arr['outgoing']),
            ]);
        }

        $mappedData = $this->normalizeAxisX($mappedData);

        $this->totalCallsRecDurationDataAVG = json_encode(ArrayHelper::merge([[
            'Date',
            'Incoming Call Duration AVG',
            [
                'type' => 'string',
                'role' => 'tooltip'
            ],
            'Outgoing Call Duration AVG',
            [
                'type' => 'string',
                'role' => 'tooltip'
            ],
        ]], $mappedData));
    }

    private function normalizeAxisX($mappedData)
    {
        if ($this->callGraphsSearch->callGraphGroupBy == CallGraphsSearch::DATE_FORMAT_HOURS) {
            $mappedData = $this->normalizeByHoursOnAxisX($mappedData);
        }

        if ($this->callGraphsSearch->callGraphGroupBy == CallGraphsSearch::DATE_FORMAT_DAYS) {
            $mappedData = $this->normalizeByDaysOnAxisX($mappedData);
        }

        if ($this->callGraphsSearch->callGraphGroupBy == CallGraphsSearch::DATE_FORMAT_WEEKS) {
            $mappedData = $this->normalizeByWeeksOnAxisX($mappedData);
        }

        if ($this->callGraphsSearch->callGraphGroupBy === CallGraphsSearch::DATE_FORMAT_WEEKDAYS) {
            $mappedData = $this->setWeekDayName($mappedData);
        }

        if ($this->callGraphsSearch->callGraphGroupBy === CallGraphsSearch::DATE_FORMAT_MONTH) {
            $mappedData = $this->setMonthName($mappedData);
            $mappedData = $this->normalizeByMonthsOnAxisX($mappedData);
        }

        if ($this->callGraphsSearch->callGraphGroupBy == CallGraphsSearch::DATE_FORMAT_HOURS_DAYS) {
            $mappedData = $this->normalizeByHoursOfDaysOnAxisX($mappedData);
        }

        if ($this->callGraphsSearch->callGraphGroupBy == CallGraphsSearch::DATE_FORMAT_WEEKDAYS) {
            $mappedData = $this->normalizeByWeekDaysOnAxisX($mappedData);
        }

        return $mappedData;
    }

    /**
     * @param array $data
     * @return array
     */
    private function normalizeByHoursOnAxisX(array $data): array
    {
        $timeRange = explode(' - ', $this->callGraphsSearch->createTimeRange);
        $groupFormat = $this->callGraphsSearch::GROUP_FORMAT_DAYS_HOURS;
        $axisX = ChartTools::getHoursRange($timeRange[0], $timeRange[1], $step = '+1 hour', $groupFormat);
        $defaultGroups = array_column($data, 0);
        $normalizedData = $data;
        foreach ($axisX as $point) {
            if (in_array($point, $defaultGroups)) {
                continue;
            } else {
                array_push($normalizedData, [$point, 0, '', 0, '']);
            }
        }

        usort($normalizedData, function ($firstElement, $secondElement) {
            $datetimeFirst = strtotime($firstElement[0]);
            $datetimeSecond = strtotime($secondElement[0]);
            return $datetimeFirst - $datetimeSecond;
        });

        return $normalizedData;
    }
    /**
     * @param array $data
     * @return array
     */
    private function normalizeByHoursOfDaysOnAxisX(array $data): array
    {
        $axisX = ChartTools::getHourRange();
        $defaultGroups = array_column($data, 0);
        $normalizedData = $data;
        foreach ($axisX as $point) {
            if (in_array($point, $defaultGroups)) {
                continue;
            } else {
                array_push($normalizedData, [$point, 0, '', 0, '']);
            }
        }

        usort($normalizedData, function ($firstElement, $secondElement) {
            $datetimeFirst = strtotime($firstElement[0]);
            $datetimeSecond = strtotime($secondElement[0]);
            return $datetimeFirst - $datetimeSecond;
        });

        return $normalizedData;
    }

    /**
     * @param array $data
     * @return array
     */
    private function normalizeByWeekDaysOnAxisX(array $data): array
    {
        $axisX = ChartTools::getWeekDaysRange();
        $defaultGroups = array_column($data, 0);
        $normalizedData = $data;
        foreach ($axisX as $point) {
            if (in_array($point, $defaultGroups)) {
                continue;
            } else {
                array_push($normalizedData, [$point, 0, '', 0, '']);
            }
        }

        usort($normalizedData, function ($a, $b) {
            return date('N', strtotime($a[0])) - date('N', strtotime($b[0]));
        });

        return $normalizedData;
    }

    /**
     * @param array $data
     * @return array
     */
    private function normalizeByDaysOnAxisX(array $data): array
    {
        $timeRange = explode(' - ', $this->callGraphsSearch->createTimeRange);
        $axisX = ChartTools::getDaysRange($timeRange[0], $timeRange[1]);
        $defaultGroups = array_column($data, 0);
        $normalizedData = $data;
        foreach ($axisX as $point) {
            if (in_array($point, $defaultGroups)) {
                continue;
            } else {
                array_push($normalizedData, [$point, 0, '', 0, '']);
            }
        }

        usort($normalizedData, function ($firstElement, $secondElement) {
            $datetimeFirst = strtotime($firstElement[0]);
            $datetimeSecond = strtotime($secondElement[0]);
            return $datetimeFirst - $datetimeSecond;
        });

        return $normalizedData;
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    private function normalizeByWeeksOnAxisX(array $data): array
    {
        $timeRange = explode(' - ', $this->callGraphsSearch->createTimeRange);
        $axisX = ChartTools::getWeeksRange(new \DateTime($timeRange[0]), new \DateTime($timeRange[1]));
        $defaultGroups = array_column($data, 0);
        $normalizedData = $data;
        foreach ($axisX as $point) {
            if (in_array($point, $defaultGroups)) {
                continue;
            } else {
                array_push($normalizedData, [$point, 0, '', 0, '']);
            }
        }

        usort($normalizedData, function ($firstElement, $secondElement) {
            $firstCriteria = explode('/', $firstElement[0]);
            $secondCriteria = explode('/', $secondElement[0]);
            $datetimeFirst = strtotime($firstCriteria[0]);
            $datetimeSecond = strtotime($secondCriteria[0]);
            return $datetimeFirst - $datetimeSecond;
        });

        return $normalizedData;
    }

    /**
     * @param array $data
     * @return array
     */
    private function normalizeByMonthsOnAxisX(array $data): array
    {
        $timeRange = explode(' - ', $this->callGraphsSearch->createTimeRange);
        $axisX = ChartTools::getMonthsRange($timeRange[0], $timeRange[1], 'Y-F');
        $defaultGroups = array_column($data, 0);
        $normalizedData = $data;

        foreach ($axisX as $point) {
            if (in_array($point, $defaultGroups)) {
                continue;
            } else {
                array_push($normalizedData, [$point, 0, '', 0, '']);
            }
        }

        usort($normalizedData, function ($firstElement, $secondElement) {
            $datetimeFirst = strtotime($firstElement[0]);
            $datetimeSecond = strtotime($secondElement[0]);
            return $datetimeFirst - $datetimeSecond;
        });

        return $normalizedData;
    }

    /**
     * @param array $data
     * @return array
     */
    private function setWeekDayName(array $data):array
    {
        $week = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($data as $key => $arr) {
            $firstKey = array_key_first($arr);
            $data[$key][$firstKey] = $week[$arr[$firstKey]];
        }

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    private function setMonthName(array $data):array
    {
        foreach ($data as $key => $arr) {
            $firstKey = array_key_first($arr);
            $data[$key][$firstKey] = date('Y-F', strtotime($data[$key][$firstKey]));
        }

        return $data;
    }

    /**
     * @return array
     */
    private function fetchGridColumns()
    {
        return [
            [
                'label' => 'Group By',
                'attribute' => 'date'
            ],
            [
                'label' => 'Incoming',
                'attribute' => 'incoming',
            ],
            [
                'label' => 'Outgoing',
                'attribute' => 'outgoing'
            ],
            [
                'label' => 'Total Calls',
                'attribute' => 'total'
            ],
            [
                'label' => 'Incoming Average',
                'attribute' => 'incomingAvg'
            ],
            [
                'label' => 'Outgoing Average',
                'attribute' => 'outgoingAvg'
            ],
            [
                'label' => 'Incoming Call Duration',
                'attribute' => 'incomingTotal',
                'value' => static function ($val) {
                    return Yii::$app->formatter->asDuration((int)$val['incomingTotal']);
                }
            ],
            [
                'label' => 'Outgoing Call Duration',
                'attribute' => 'outgoingTotal',
                'value' => static function ($val) {
                    return Yii::$app->formatter->asDuration((int)$val['outgoingTotal']);
                }
            ],
            [
                'label' => 'Total Call Duration',
                'attribute' => 'totalCallsDuration',
                'value' => static function ($val) {
                    return Yii::$app->formatter->asDuration((int)$val['totalCallsDuration']);
                }
            ],
            [
                'label' => 'Incoming Call Duration Average',
                'attribute' => 'incomingAvgDuration',
                'value' => static function ($val) {
                    return Yii::$app->formatter->asDuration((int)$val['incomingAvgDuration']);
                }
            ],
            [
                'label' => 'Outgoing Call Duration Average',
                'attribute' => 'outgoingAvgDuration',
                'value' => static function ($val) {
                    return Yii::$app->formatter->asDuration((int)$val['outgoingAvgDuration']);
                }
            ],
            [
                'label' => 'Total Call Duration Average',
                'attribute' => 'totalAvgDuration',
                'value' => static function ($val) {
                    return Yii::$app->formatter->asDuration((int)$val['totalAvgDuration']);
                }
            ]
        ];
    }
}
