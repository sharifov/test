<?php

namespace sales\viewModel\call;

use DateInterval;
use DatePeriod;
use DateTime;
use sales\entities\call\CallGraphsSearch;
use Yii;
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
 */
class ViewModelTotalCallGraph
{
	public $totalCallsGraphData;

	public $totalCallsGraphDataAvg;

	public $totalCallsRecDurationData;

	public $totalCallsRecDurationDataAVG;

	public $callGraphsSearch;

	public $callData;

	/**
	 * ViewModelTotalCallGraph constructor.
	 * @param array $callData
	 * @param CallGraphsSearch $callGraphsSearch
	 */
	public function __construct(array $callData, CallGraphsSearch $callGraphsSearch)
	{
		$this->callData = $callData;
		$this->callGraphsSearch = $callGraphsSearch;

		$this->formatTotalCallsGraphData();
		$this->formatTotalCallsGraphDataAvg();
		$this->totalCallsRecDurationData();
		$this->totalCallsRecDurationDataAVG();
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
		$data = array_map( function ($arr) {

			$delimiter = 1;
			if ($this->callGraphsSearch->callGraphGroupBy === CallGraphsSearch::DATE_FORMAT_HOURS_DAYS) {
				$delimiter = $this->countHourDayInDateRange($arr['created_formatted'], $this->callGraphsSearch->createTimeStart, $this->callGraphsSearch->createTimeEnd);
			} else if ($this->callGraphsSearch->callGraphGroupBy === CallGraphsSearch::DATE_FORMAT_WEEKDAYS) {
				$delimiter = $this->countWeekDayInDateRange($arr['created_formatted'], $this->callGraphsSearch->createTimeStart, $this->callGraphsSearch->createTimeEnd);
			}

			return [
				$arr['created_formatted'],
				$arr['incoming_avg'] = $arr['incoming'] / $delimiter,
				$arr['outgoing_avg'] = $arr['outgoing'] / $delimiter,
				$arr['total_calls_avg'] = ($arr['incoming'] + $arr['outgoing']) / $delimiter
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
}