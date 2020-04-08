<?php

namespace modules\flight\models\forms;

use modules\flight\models\FlightSegment;
use modules\flight\src\helpers\FlightSegmentHelper;
use common\components\validators\IataValidator;

/**
 * Class FlightSegmentForm
 * @package modules\flight\models\forms
 */
class FlightSegmentForm extends FlightSegment
{
	/**
	 * @return array
	 */
	public function rules(): array
	{
		return [
			[['fs_origin_iata', 'fs_destination_iata'], 'required'],
			[['fs_origin_iata', 'fs_destination_iata'], IataValidator::class],
			[['fs_origin_iata', 'fs_destination_iata'], 'string', 'max' => 3],
			['fs_destination_iata', 'compare', 'compareAttribute' => 'fs_origin_iata', 'operator' => '!='],
			[['fs_origin_iata', 'fs_destination_iata'], 'filter', 'filter' => static function($value) {
				return strtoupper($value);
			}],
			[['fs_origin_iata_label', 'fs_destination_iata_label'], 'string', 'max' => 255],

			['fs_departure_date', 'required'],
			['fs_departure_date', 'date', 'format' => 'php:d-M-Y'],
			['fs_departure_date', 'filter', 'filter' => static function($value) {
				return date('Y-m-d', strtotime($value));
			}],

			['fs_flex_days', 'integer'],
			['fs_flex_days', 'in', 'range' => array_keys(FlightSegmentHelper::flexibilityList())],
			['fs_flex_days', 'filter', 'filter' => 'intval'],

			['fs_flex_type_id', 'string', 'length' => [1, 3]],
			['fs_flex_type_id', 'in', 'range' => array_keys(FlightSegmentHelper::flexibilityTypeList())],
		];
	}
}