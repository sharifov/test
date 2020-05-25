<?php

namespace sales\viewModel\call;

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
    public function __construct(SqlDataProvider $callData,
                                CallGraphsSearch $callGraphsSearch)
    {
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

        foreach ($this->callData as $rowIndex){
            foreach ($this->callData as $row){
                if ($rowIndex['group'] == $row['group']) {
                    $data['date'] = $row['group'];
                    if ($row['callType'] === 'in'){
                        $data['incoming'] = $row['totalCalls'];
                        $data['incomingAvg'] = $row['avgCallsPerGroup'];
                        $data['incomingTotal'] = $row['totalCallsDuration'];
                        $data['incomingAvgDuration'] = $row['avgCallDuration'];
                    }

                    if ($row['callType'] === 'out'){
                        $data['outgoing'] = $row['totalCalls'];
                        $data['outgoingAvg'] = $row['avgCallsPerGroup'];
                        $data['outgoingTotal'] = $row['totalCallsDuration'];
                        $data['outgoingAvgDuration'] = $row['avgCallDuration'];
                    }

                    if ($row['callType'] === 'total'){
                        $data['total'] = $row['totalCalls'];
                        $data['totalCallsDuration'] = $row['totalCallsDuration'];
                        $data['totalAvgDuration'] = $row['avgCallDuration'];
                    }
                    $finalData[$row['group']] = $data;
                }
            }
        }

        foreach ($finalData as $arr){
            array_push($mappedData, [
                'date' => $arr['date'],
                'incoming' => isset($arr['incoming']) ? (int)$arr['incoming'] : 0,
                'outgoing' => isset($arr['outgoing']) ? (int)$arr['outgoing'] : 0,
                'total' => (int)$arr['total'],
                'incomingAvg' => isset($arr['incomingAvg']) ? (int)$arr['incomingAvg'] : 0,
                'outgoingAvg' => isset($arr['outgoingAvg']) ? (int)$arr['outgoingAvg'] : 0,
                'incomingTotal' => isset($arr['incomingTotal']) ? (int)$arr['incomingTotal'] : 0,
                'outgoingTotal' => isset($arr['outgoingTotal']) ? (int)$arr['outgoingTotal'] : 0,
                'totalCallsDuration' => (int)$arr['totalCallsDuration'],
                'incomingAvgDuration' => isset($arr['incomingAvgDuration']) ? (int)$arr['incomingAvgDuration'] : 0,
                'outgoingAvgDuration' => isset($arr['outgoingAvgDuration']) ? (int)$arr['outgoingAvgDuration'] : 0,
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

        foreach ($this->callData as $rowIndex){
            foreach ($this->callData as $row){
                if ($rowIndex['group'] == $row['group']) {
                    $data['date'] = $row['group'];
                    if ($row['callType'] === 'in'){
                        $data['incoming'] = $row['totalCalls'];
                    }

                    if ($row['callType'] === 'out'){
                        $data['outgoing'] = $row['totalCalls'];
                    }

                    /*if ($row['callType'] === 'total'){
                        $data['total'] = $row['totalCalls'];
                    }*/
                    $finalData[$row['group']] = $data;
                }
            }
        }

        foreach ($finalData as $arr){
            array_push($mappedData, [
                $arr['date'],
                isset($arr['incoming']) ? (int)$arr['incoming'] : 0,
                isset($arr['outgoing']) ? (int)$arr['outgoing'] : 0,
                /*(int)$arr['total']*/
                ]);
        }

        if ($this->callGraphsSearch->callGraphGroupBy === CallGraphsSearch::DATE_FORMAT_WEEKDAYS) {
            $mappedData = $this->setWeekDayName($mappedData);
        }

        if ($this->callGraphsSearch->callGraphGroupBy === CallGraphsSearch::DATE_FORMAT_MONTH) {
            $mappedData = $this->setMonthName($mappedData);
        }

        $this->totalCallsGraphData = json_encode(ArrayHelper::merge([[
            'Date',
            'Incoming',
            'Outgoing',
            /*'Total',*/
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

        foreach ($this->callData as $rowIndex){
            foreach ($this->callData as $row){
                if ($rowIndex['group'] == $row['group']) {
                    $data['date'] = $row['group'];
                    if ($row['callType'] === 'in'){
                        $data['incoming'] = $row['avgCallsPerGroup'];
                    }

                    if ($row['callType'] === 'out'){
                        $data['outgoing'] = $row['avgCallsPerGroup'];
                    }

                    /*if ($row['callType'] === 'total'){
                        $data['total'] = $row['avgCallsPerGroup'];
                    }*/
                    $finalData[$row['group']] = $data;
                }
            }
        }

        foreach ($finalData as $arr){
            array_push($mappedData, [
                $arr['date'],
                isset($arr['incoming']) ? (int)$arr['incoming'] : 0,
                isset($arr['outgoing']) ? (int)$arr['outgoing'] : 0,
                /*(int)$arr['total']*/
            ]);
        }

        if ($this->callGraphsSearch->callGraphGroupBy === CallGraphsSearch::DATE_FORMAT_WEEKDAYS) {
            $mappedData = $this->setWeekDayName($mappedData);
        }

        if ($this->callGraphsSearch->callGraphGroupBy === CallGraphsSearch::DATE_FORMAT_MONTH) {
            $mappedData = $this->setMonthName($mappedData);
        }

        $this->totalCallsGraphDataAvg = json_encode(ArrayHelper::merge([[
            'Date',
            'Incoming',
            'Outgoing',
            /*'Total',*/
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

        foreach ($this->callData as $rowIndex){
            foreach ($this->callData as $row){
                if ($rowIndex['group'] == $row['group']) {
                    $data['date'] = $row['group'];
                    if ($row['callType'] === 'in'){
                        $data['incoming'] = $row['totalCallsDuration'];
                    }

                    if ($row['callType'] === 'out'){
                        $data['outgoing'] = $row['totalCallsDuration'];
                    }

                    /*if ($row['callType'] === 'total'){
                        $data['total'] = $row['totalCallsDuration'];
                    }*/
                    $finalData[$row['group']] = $data;
                }
            }
        }

        foreach ($finalData as $arr){
            array_push($mappedData, [
                $arr['date'],
                isset($arr['incoming']) ? (int)$arr['incoming'] : 0,
                'Incoming Call Duration: ' . Yii::$app->formatter->asDuration(isset($arr['incoming']) ? (int)$arr['incoming'] : 0),
                isset($arr['outgoing']) ? (int)$arr['outgoing'] : 0,
                'Outgoing Call Duration: ' . Yii::$app->formatter->asDuration(isset($arr['outgoing']) ? (int)$arr['outgoing'] : 0),
                /*(int)$arr['total'],
                'Total Call Duration: ' . Yii::$app->formatter->asDuration((int)$arr['total']),*/
            ]);
        }

        if ($this->callGraphsSearch->callGraphGroupBy === CallGraphsSearch::DATE_FORMAT_WEEKDAYS) {
            $mappedData = $this->setWeekDayName($mappedData);
        }

        if ($this->callGraphsSearch->callGraphGroupBy === CallGraphsSearch::DATE_FORMAT_MONTH) {
            $mappedData = $this->setMonthName($mappedData);
        }

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
            /*'Total Call Duration',
            [
                'type' => 'string',
                'role' => 'tooltip'
            ],*/
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

        foreach ($this->callData as $rowIndex){
            foreach ($this->callData as $row){
                if ($rowIndex['group'] == $row['group']) {
                    $data['date'] = $row['group'];
                    if ($row['callType'] === 'in'){
                        $data['incoming'] = $row['avgCallDuration'];
                    }

                    if ($row['callType'] === 'out'){
                        $data['outgoing'] = $row['avgCallDuration'];
                    }

                    /*if ($row['callType'] === 'total'){
                        $data['total'] = $row['avgCallDuration'];
                    }*/
                    $finalData[$row['group']] = $data;
                }
            }
        }

        foreach ($finalData as $arr){
            array_push($mappedData, [
                $arr['date'],
                isset($arr['incoming']) ? (int)$arr['incoming'] : 0,
                'Incoming Call Duration AVG: ' . Yii::$app->formatter->asDuration(isset($arr['incoming']) ? (int)$arr['incoming'] : 0),
                isset($arr['outgoing']) ? (int)$arr['outgoing'] : 0,
                'Outgoing Call Duration AVG: ' . Yii::$app->formatter->asDuration(isset($arr['outgoing']) ? (int)$arr['outgoing'] : 0),
                /*(int)$arr['total'],
                'Total Call Duration AVG: ' . Yii::$app->formatter->asDuration((int)$arr['total']),*/
            ]);
        }

        if ($this->callGraphsSearch->callGraphGroupBy === CallGraphsSearch::DATE_FORMAT_WEEKDAYS) {
            $mappedData = $this->setWeekDayName($mappedData);
        }

        if ($this->callGraphsSearch->callGraphGroupBy === CallGraphsSearch::DATE_FORMAT_MONTH) {
            $mappedData = $this->setMonthName($mappedData);
        }

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
            /*'Total Call Duration AVG',
            [
                'type' => 'string',
                'role' => 'tooltip'
            ],*/
        ]], $mappedData));
    }


    /**
     * @param array $data
     * @return array
     */
    private function setWeekDayName(array $data):array
    {
        $week = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($data as $key => $arr){
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
        foreach ($data as $key => $arr){
            $firstKey = array_key_first($arr);
            $data[$key][$firstKey] = date('Y-F', strtotime($data[$key][$firstKey]));
        }

        return $data;
    }

    /**
     * @param array $callData
     * @return $this
     * @throws \Exception
     */
    /*private function calcTotalCallsAverage(array &$callData): void
    {
        foreach ($callData as $key => $item) {
            $delimiter = 1;
            if ($this->callGraphsSearch->callGraphGroupBy === CallGraphsSearch::DATE_FORMAT_HOURS_DAYS) {
                $delimiter = $this->countHourDayInDateRange($item['created_formatted'], $this->callGraphsSearch->createTimeStart, $this->callGraphsSearch->createTimeEnd);
            } else if ($this->callGraphsSearch->callGraphGroupBy === CallGraphsSearch::DATE_FORMAT_WEEKDAYS) {
                $delimiter = $this->countWeekDayInDateRange($item['created_formatted'], $this->callGraphsSearch->createTimeStart, $this->callGraphsSearch->createTimeEnd);
            }

            $callData[$key]['incoming_avg'] = $item['incoming'] / $delimiter;
            $callData[$key]['outgoing_avg'] = $item['outgoing'] / $delimiter;
            $callData[$key]['total_calls_avg'] = ($item['incoming'] + $item['outgoing']) / $delimiter;
        }
    }*/

    /**
     * How many times the hour of the day repeat in the datetime range
     *
     * @param string $hour
     * @param string $dateStart
     * @param string $dateEnd
     * @return int
     * @throws \Exception
     */
    /*private function countHourDayInDateRange(string $hour, string $dateStart, string $dateEnd): int
    {
        $dateStart = new DateTime($dateStart);
        $dateEnd = new DateTime($dateEnd);
        $d1 = new DateTime($dateStart->format('Y-m-d '.$hour));
        $d2 = new DateTime($dateEnd->format('Y-m-d '.$hour));

        $interval = $dateStart->diff($dateEnd);
        if ( ($d1 < $dateStart && $d2 < $dateEnd) || ($d1 > $dateStart && $d2 > $dateEnd) ) {
            $hourCount = $interval->d;
        } else {
            $hourCount = $interval->d+1;
        }

        return $hourCount;
    }*/

    /**
     * @param string $dayName
     * @param string $dateStart
     * @param string $dateEnd
     * @return int
     * @throws \Exception
     */
    /*private function countWeekDayInDateRange(string $dayName, string $dateStart, string $dateEnd): int
    {
        $week = ['Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0, 'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0];

        $d1 = new DateTime($dateStart);
        $d2 = new DateTime($dateEnd);

        $interval = DateInterval::createFromDateString('1 day');
        $period   = new DatePeriod($d1, $interval, $d2);

        foreach ($period as $date) {
            $week[$date->format('l')]++;
        }

        return $week[$dayName];
    }*/

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
            /*[
                'label' => 'Total Average',
                'attribute' => 'total_calls_avg'
            ],*/
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