<?php

namespace sales\services\cases;

use common\models\CaseSale;
use Exception;
use http\Exception\RuntimeException;
use sales\repositories\cases\CasesSaleRepository;

class CasesSaleService
{
	private const FORMAT_PASSENGERS_DATA = [
		'meal' => 'formatByCountSegments',
		'wheelchair' => 'formatByCountSegments',
		'ff_numbers' => 'formatByAirline',
		'kt_numbers' => 'formatByAirline',
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
	 * @var array
	 */
	private $namref = [];

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
	 * @throws Exception
	 */
	public function prepareSaleData(CaseSale $caseSale): array
	{
		$originalData = json_decode( (string)$caseSale->css_sale_data, true );
		$updatedData = json_decode( (string)$caseSale->css_sale_data_updated, true );

		$difference = $this->compareSaleData($originalData, $updatedData);

		if (!empty($originalData['passengers'])) {
			throw new \RuntimeException('Sale Info: not found passengers data while preparing data for sync with B/0', 10);
		}

		$this->bufferPassengerNameref($originalData['passengers'])->preparePassengersData($difference);

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
	 * @param CaseSale $caseSale
	 * @return CasesSaleService
	 */
	public function setSegments(CaseSale $caseSale): CasesSaleService
	{
		$this->segments = $this->getSegments($caseSale);

		return $this;
	}

	/**
	 * @param CaseSale $caseSale
	 * @return bool
	 */
	public function isDataBackedUpToOriginal(CaseSale $caseSale): bool
	{
		$oldData = json_decode((string)$caseSale->css_sale_data, true);
		$newData = json_decode((string)$caseSale->css_sale_data_updated, true);
		$difference = $this->compareSaleData($oldData, $newData);

		return !$difference ? true : false;
	}

	/**
	 * @param array $saleDataDiff
	 * @throws Exception
	 */
	private function preparePassengersData(array &$saleDataDiff): void
	{
		if (isset($saleDataDiff['passengers']) && !empty($this->namref)) {
			foreach ($saleDataDiff['passengers'] as $key => $passenger) {
//				$this->formatPassengersData($passenger);

				unset($saleDataDiff['passengers'][$key]);
				$saleDataDiff['passengers'][$this->namref[$key]] = $passenger;
			}
		} else {
			throw new \RuntimeException('Sale info doesnt have passengers or passengers nameref');
		}
	}

	/**
	 * @param array $passenger
	 */
	public function formatPassengersData(array &$passenger): void
	{
		foreach ($passenger as $key => $value) {
			if (array_key_exists($key, self::FORMAT_PASSENGERS_DATA) && method_exists($this, self::FORMAT_PASSENGERS_DATA[$key])) {
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
	 * @param array $passengers
	 * @return CasesSaleService
	 */
	private function bufferPassengerNameref(array $passengers): CasesSaleService
	{
		foreach ($passengers as $key => $passenger) {
			if (empty($passenger['nameref'])) {
				throw new \RuntimeException('Sale info: nameref is not found in passengers data');
			}
			$this->namref[$key] = $passenger['nameref'];
		}

		return $this;
	}

	/**
	 * @param array $oldData
	 * @param array $newData
	 * @return array
	 */
	public function compareSaleData(array $oldData, array $newData): array
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
			} elseif ((!array_key_exists($firstKey, $oldData) || $oldData[$firstKey] != $firstValue)) {
				if (!empty($firstValue) || !empty($oldData[$firstKey])) {
					$difference[$firstKey] = $firstValue;
				}
			}
		}
		return $difference;
	}
}