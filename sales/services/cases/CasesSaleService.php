<?php

namespace sales\services\cases;

use common\models\CaseSale;
use sales\repositories\cases\CasesSaleRepository;
use yii\helpers\ArrayHelper;

class CasesSaleService
{
	private const FORMAT_PASSENGERS_DATA = [
		'meal' => 'formatByCountSegments',
		'wheelchair' => 'formatByCountSegments',
		'ff_numbers' => 'formatByAirline',
		'ktn_numbers' => 'formatByAirline',
	];

	/**
	 * @var CasesSaleRepository
	 */
	private $casesSaleRepository;

	/**
	 * @var array
	 */
	private $segments = [];

	/**
	 * CasesSaleService constructor.
	 * @param CasesSaleRepository $casesSaleRepository
	 */
	public function __construct(CasesSaleRepository $casesSaleRepository)
	{
		$this->casesSaleRepository = $casesSaleRepository;
	}

	/**
	 * @param CaseSale $caseSale
	 * @return array
	 */
	public function prepareSaleData(CaseSale $caseSale): array
	{
		$originalData = json_decode( (string)$caseSale->css_sale_data, true );
		$updatedData = json_decode( (string)$caseSale->css_sale_data_updated, true );

		$difference = $this->compareSaleData($originalData, $updatedData);

		$this->segments = $this->getSegments($caseSale);

		$this->preparePassengersData($difference);

		return $difference;
	}

	/**
	 * @param CaseSale $caseSale
	 * @return array
	 */
	public function getSegments(CaseSale $caseSale): array
	{
		$updatedData = json_decode((string)$caseSale->css_sale_data_updated, true);

		$segments = [];

		foreach ($updatedData['itinerary'] as $itinerary) {
			foreach ($itinerary['segments'] as $segment) {
				$segments[] = $segment;
			}
		}

		return $segments;
	}

	/**
	 * @param array $saleDataDiff
	 */
	private function preparePassengersData(array &$saleDataDiff): void
	{
		if (isset($saleDataDiff['passengers'])) {
			foreach ($saleDataDiff['passengers'] as $key => $passenger) {
				$this->formatPassengersData($passenger);

				unset($saleDataDiff['passengers'][$key]);
				$saleDataDiff['passengers'][++$key . '.1'] = $passenger;
			}
		}
	}

	/**
	 * @param array $passenger
	 */
	private function formatPassengersData(array &$passenger): void
	{
		foreach ($passenger as $key => $value) {
			if (key_exists($key, self::FORMAT_PASSENGERS_DATA) && method_exists($this, self::FORMAT_PASSENGERS_DATA[$key])) {
				$this->{self::FORMAT_PASSENGERS_DATA[$key]}($passenger, $key);
			}
		}
	}

	/**
	 * @param array $passenger
	 * @param string $key
	 */
	private function formatByCountSegments(array &$passenger, string $key): void
	{
		$value = $passenger[$key];
		$passenger[$key] = [];
		foreach ($this->segments as $segmentKey => $segment) {
			$passenger[$key][$segmentKey+1] = $value;
		}
	}

	/**
	 * @param array $passenger
	 * @param string $key
	 */
	private function formatByAirline(array &$passenger, string $key): void
	{
		$value = $passenger[$key];
		$passenger[$key] = [];
		foreach ($this->segments as $segmentKey => $segment) {
			$passenger[$key][$segment['airline']] = $value;
		}
	}

	/**
	 * @param array $oldData
	 * @param array $newData
	 * @param bool $checkForEmpty
	 * @return array
	 */
	public function compareSaleData(array $oldData, array $newData, bool $checkForEmpty = true): array
	{
		$difference = [];
		foreach ($newData as $firstKey => $firstValue) {
			if (is_array($firstValue)) {
				if (!array_key_exists($firstKey, $oldData) || !is_array($oldData[$firstKey])) {
					$difference[$firstKey] = '';
				} else {
					$newDiff = $this->compareSaleData($oldData[$firstKey], $firstValue);
					if (!empty($newDiff)) {
						$difference[$firstKey] = $newDiff;
					}
				}
			} else {
				if ((!array_key_exists($firstKey, $oldData) || $oldData[$firstKey] != $firstValue) && !empty($newData[$firstKey])) {
					$difference[$firstKey] = $newData[$firstKey];
				}
			}
		}
		return $difference;
	}
}