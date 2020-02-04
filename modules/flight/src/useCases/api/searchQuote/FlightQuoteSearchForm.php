<?php

namespace modules\flight\src\useCases\api\searchQuote;

use modules\flight\models\Flight;
use modules\flight\models\FlightQuote;
use sales\helpers\app\AppHelper;
use yii\base\Model;

/**
 * Class FlightQuoteSearchForm
 * @package modules\flight\src\useCases\api\searchQuote
 *
 * @property array $fareType
 * @property array $stops
 * @property int $price
 * @property array $airlines
 * @property string $tripDuration
 * @property bool $baggage
 * @property bool $airportChange
 * @property string $sortBy
 */
class FlightQuoteSearchForm extends Model
{
	/**
	 * @var array
	 */
	public $fareType;

	/**
	 * @var int
	 */
	public $price;

	/**
	 * @var array
	 */
	public $stops;

	/**
	 * @var array
	 */
	public $airlines;

	/**
	 * @var string
	 */
	public $tripDuration;

	/**
	 * @var bool
	 */
	public $baggage;

	/**
	 * @var bool
	 */
	public $airportChange;

	/**
	 * @var string
	 */
	public $sortBy;


	/**
	 * @return array
	 */
	public function rules(): array
	{
		return [
			[['fareType', 'airlines', 'tripDuration', 'stops', 'baggage', 'airportChange', 'sortBy'], 'safe'],
			['price', 'filter', 'filter' => 'intval'],
		];
	}

	/**
	 * @return array
	 */
	public function scenarios(): array
	{
		return Model::scenarios();
	}

	/**
	 * @return array
	 * @throws \ReflectionException
	 */
	public function getFilters(): array
	{
		return [ (new \ReflectionClass($this))->getShortName()  => $this->toArray()];
	}

	/**
	 * @return string
	 */
	public function getSortBy(): string
	{
		return FlightQuote::getSortAttributeNameById($this->sortBy) ?? FlightQuote::getDefaultSortAttributeName();
	}

	public function getSortType(): int
	{
		return FlightQuote::getSortTypeBySortId($this->sortBy) ?? FlightQuote::getDefaultSortType();
	}

	/**
	 * @param array $quotes
	 * @return array
	 */
	public function applyFilters(array $quotes): array
	{
		if (!empty($this->fareType)) {
			$quotes['results'] = AppHelper::filterByArray($quotes['results'], 'fareType', $this->fareType);
		}

		if (!empty($this->airlines)) {
			$quotes['results'] = AppHelper::filterByArray($quotes['results'], 'validatingCarrier', $this->airlines);
		}

		if (!empty($this->price)) {
			$quotes['results'] = AppHelper::filterByRange($quotes['results'], 'price', null, $this->price);
		}

		if (!$this->stops !== null && $this->stops != '') {
			$quotes['results'] = array_filter($quotes['results'], function ($item) {
				$cnt = 0;
				foreach ($item['stops'] as $stop) {
					if ($stop <= $this->stops) {
						$cnt++;
					}
				}

				return count($item['stops']) === $cnt;
			}, ARRAY_FILTER_USE_BOTH);
		}

		if ($this->airportChange) {
			$quotes['results'] = AppHelper::filterByValue($quotes['results'], 'airportChange', !$this->airportChange);
		}

		if ($this->baggage) {
			$quotes['results'] = AppHelper::filterByRange($quotes['results'], 'bagFilter', (int)$this->baggage);
		}

		if ($this->tripDuration) {
			$quotes['results'] = array_filter($quotes['results'], function ($item) {
				$cnt = 0;
				foreach ($item['duration'] as $duration) {
					if ($duration <= $this->tripDuration) {
						$cnt++;
					}
				}

				return count($item['duration']) === $cnt;
			}, ARRAY_FILTER_USE_BOTH);
		}

		return $quotes;
	}
}