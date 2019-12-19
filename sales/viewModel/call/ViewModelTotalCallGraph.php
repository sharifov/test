<?php

namespace sales\viewModel\call;

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
 * @property CallGraphsSearch $model
 */
class ViewModelTotalCallGraph
{
	public $totalCallsGraphData;

	public $totalCallsGraphDataAvg;

	public $totalCallsRecDurationData;

	public $totalCallsRecDurationDataAVG;

	public $model;

	public $callData;

	public function __construct(array $callData)
	{
		$this->callData = $callData;

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
		$data = array_map( static function ($arr) {
			return [$arr['created_formatted'],(int)$arr['incoming_avg'], (int)$arr['outgoing_avg'], (int)$arr['total_calls_avg']];
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
}