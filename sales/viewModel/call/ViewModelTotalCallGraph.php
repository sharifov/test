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

		$this->calcTotalCallsAverage($this->callData);

		$this->formatTotalCallsGraphData();
		$this->formatTotalCallsGraphDataAvg();
		$this->totalCallsRecDurationData();
		$this->totalCallsRecDurationDataAVG();

		$this->dataProvider = (new ArrayDataProvider(['allModels' => $this->callData]));
		$this->gridColumns = $this->fetchGridColumns();
	}

	/**
	 * @return void
	 */
	private function formatTotalCallsGraphData(): void
	{
		$data = array_map( static function ($arr) {
			return [$arr['created_formatted'],(int)$arr['incoming'], (int)$arr['outgoing'], (int)$arr['total_calls']];
		}, $this->callData);
		$this->totalCallsGraphData = json_encode(ArrayHelper::merge([[
			'Date',
			'Incoming',
			'Outgoing',
			'Total',
		]], $data));
	}

	/**
	 * @return void
	 */
	private function formatTotalCallsGraphDataAvg(): void
	{
		$data = array_map( static function ($arr) {
			return [
				$arr['created_formatted'],
				$arr['incoming_avg'],
				$arr['outgoing_avg'],
				$arr['total_calls_avg'],
			];
		}, $this->callData);
		$this->totalCallsGraphDataAvg = json_encode(ArrayHelper::merge([[
			'Date',
			'Incoming Avg',
			'Outgoing Avg',
			'Total Avg',
		]], $data));
	}

	/**
	 * @return void
	 */
	private function totalCallsRecDurationData(): void
	{
		$data = array_map( static function ($arr) {
			return [
				$arr['created_formatted'],
				(int)$arr['in_rec_duration'],
				'Incoming Call Duration: ' . Yii::$app->formatter->asDuration((int)$arr['in_rec_duration']),
				(int)$arr['out_rec_duration'],
				'Outgoing Call Duration: ' . Yii::$app->formatter->asDuration((int)$arr['out_rec_duration']),
				(int)$arr['total_rec_duration'],
				'Total Call Duration: ' . Yii::$app->formatter->asDuration((int)$arr['total_rec_duration']),
			];
		}, $this->callData);
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
			'Total Call Duration',
			[
				'type' => 'string',
				'role' => 'tooltip'
			],
		]], $data));
	}

	/**
	 * @return void
	 */
	private function totalCallsRecDurationDataAVG(): void
	{
		$data = array_map( static function ($arr) {
			return [
				$arr['created_formatted'],
				(int)$arr['incoming_duration_avg'],
				'Incoming Call Duration AVG: ' . Yii::$app->formatter->asDuration((int)$arr['incoming_duration_avg']),
				(int)$arr['outgoing_duration_avg'],
				'Outgoing Call Duration AVG: ' . Yii::$app->formatter->asDuration((int)$arr['outgoing_duration_avg']),
				(int)$arr['total_rec_duration_avg'],
				'Total Call Duration AVG: ' . Yii::$app->formatter->asDuration((int)$arr['total_rec_duration_avg']),
			];
		}, $this->callData);
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
			'Total Call Duration AVG',
			[
				'type' => 'string',
				'role' => 'tooltip'
			],
		]], $data));
	}

	/**
	 * @param array $callData
	 * @return $this
	 * @throws \Exception
	 */
	private function calcTotalCallsAverage(array &$callData): void
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
	}

	/**
	 * How many times the hour of the day repeat in the datetime range
	 *
	 * @param string $hour
	 * @param string $dateStart
	 * @param string $dateEnd
	 * @return int
	 * @throws \Exception
	 */
	private function countHourDayInDateRange(string $hour, string $dateStart, string $dateEnd): int
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
	}

	/**
	 * @param string $dayName
	 * @param string $dateStart
	 * @param string $dateEnd
	 * @return int
	 * @throws \Exception
	 */
	private function countWeekDayInDateRange(string $dayName, string $dateStart, string $dateEnd): int
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
	}

	private function fetchGridColumns()
	{
		return [
			[
				'label' => 'Group By',
				'attribute' => 'created_formatted'
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
				'attribute' => 'total_calls'
			],
			[
				'label' => 'Incoming Average',
				'attribute' => 'incoming_avg'
			],
			[
				'label' => 'Outgoing Average',
				'attribute' => 'outgoing_avg'
			],
			[
				'label' => 'Total Average',
				'attribute' => 'total_calls_avg'
			],
			[
				'label' => 'Incoming Call Duration',
				'attribute' => 'in_rec_duration',
				'value' => static function ($val) {
					return Yii::$app->formatter->asDuration((int)$val['in_rec_duration']);
				}
			],
			[
				'label' => 'Outgoing Call Duration',
				'attribute' => 'out_rec_duration',
				'value' => static function ($val) {
					return Yii::$app->formatter->asDuration((int)$val['out_rec_duration']);
				}
			],
			[
				'label' => 'Total Call Duration',
				'attribute' => 'total_rec_duration',
				'value' => static function ($val) {
					return Yii::$app->formatter->asDuration((int)$val['total_rec_duration']);
				}
			],
			[
				'label' => 'Incoming Call Duration Average',
				'attribute' => 'incoming_duration_avg',
				'value' => static function ($val) {
					return Yii::$app->formatter->asDuration((int)$val['incoming_duration_avg']);
				}
			],
			[
				'label' => 'Outgoing Call Duration Average',
				'attribute' => 'outgoing_duration_avg',
				'value' => static function ($val) {
					return Yii::$app->formatter->asDuration((int)$val['outgoing_duration_avg']);
				}
			],
			[
				'label' => 'Total Call Duration Average',
				'attribute' => 'total_rec_duration_avg',
				'value' => static function ($val) {
					return Yii::$app->formatter->asDuration((int)$val['total_rec_duration_avg']);
				}
			]
		];
	}
}