<?php

namespace sales\forms\api\searchQuote;

use common\models\Quote;
use sales\helpers\app\AppHelper;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class FlightQuoteSearchForm
 * @package modules\flight\src\useCases\api\searchQuote
 *
 * @property array $fareType
 * @property array $stops
 * @property int $price
 * @property array $airlines
 * @property bool $baggage
 * @property bool $airportChange
 * @property string $sortBy
 * @property string $topCriteria
 * @property mixed $rank
 * @property string $departure
 * @property string $departureStartTimeList
 * @property string $departureEndTimeList
 * @property int $arrivalMin
 * @property int $arrivalMax
 * @property string $arrivalStartTimeList
 * @property string $arrivalEndTimeList
 * @property string $filterIsShown
 */
class FlightQuoteSearchForm extends Model
{
    public $fareType;

    public $price;

    public $stops;

    public $airlines;

    public $tripMaxDurationHours;

    public $tripMaxDurationMinutes;

    public $baggage;

    public $airportChange;

    public $airportExactMatch;

    public $excludeConnectionAirports;

    public $sortBy;

    public $topCriteria;

    public $rank = '0-10';

    public $departureStartTimeList;

    public $departureEndTimeList;

    public $arrivalStartTimeList;

    public $arrivalEndTimeList;

    public $filterIsShown = 0;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                [
                    'fareType', 'airlines', 'tripMaxDurationHours', 'tripMaxDurationMinutes', 'stops',
                    'baggage', 'airportChange', 'airportExactMatch', 'excludeConnectionAirports', 'sortBy',
                    'topCriteria', 'rank', 'departureStartTimeList','departureEndTimeList', 'arrivalStartTimeList',
                    'arrivalEndTimeList', 'filterIsShown'
                ], 'safe'],
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
    public function getSortBy(): ?string
    {
        return Quote::getSortAttributeNameById($this->sortBy);
    }

    public function getSortType(): ?int
    {
        return Quote::getSortTypeBySortId($this->sortBy);
    }

    /**
     * @param array $quotes
     * @return array
     * @throws \Exception
     */
    public function applyFilters(array $quotes, $leadFlight = null): array
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
                if (!empty($item['stops'])) {
                    foreach ($item['stops'] as $stop) {
                        if ($stop <= $this->stops) {
                            $cnt++;
                        }
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

        if ($this->rank) {
            $ranks = explode('-', $this->rank);
            $quotes['results'] = array_filter($quotes['results'], static function ($item) use ($ranks) {
                $rank = number_format($item['rank'], 1, '.', '');
                return $rank >= $ranks[0] && $rank <= $ranks[1];
            });
        }

        if (!empty($this->excludeConnectionAirports)) {
            $quotes['results'] = array_filter($quotes['results'], function ($item) {
                $item['showed'] = true;
                if (!empty($item['trips'])) {
                    foreach ($item['trips'] as $trip) {
                        if (!empty($trip['segments'])) {
                            foreach ($trip['segments'] as $segment) {
                                if (in_array($segment['departureAirportCode'], $this->excludeConnectionAirports) || in_array($segment['arrivalAirportCode'], $this->excludeConnectionAirports)) {
                                    $item['showed'] = false;
                                }
                            }
                        }
                    }
                }
                return $item['showed'];
            }, ARRAY_FILTER_USE_BOTH);
        }

        if ($this->topCriteria) {
            $quotes['results'] = AppHelper::filterByArrayContainValues($quotes['results'], 'topCriteria', $this->topCriteria);
        }

        if ($this->getSortBy()) {
            ArrayHelper::multisort($quotes['results'], $this->getSortBy(), $this->getSortType());
        }

        // Max duration
        $quotes['results'] = array_filter($quotes['results'], function ($item) {
            $item['showed'] = true;
            if (!empty($item['trips'])) {
                foreach ($item['trips'] as $tripKey => $trip) {
                    if (!empty($this->tripMaxDurationHours[$tripKey])) {
                        $requestedDuration = intval($this->tripMaxDurationHours[$tripKey]) * 60 + intval($this->tripMaxDurationMinutes[$tripKey] ?? 0);
                        if ($requestedDuration && $trip['duration'] > $requestedDuration) {
                            $item['showed'] = false;
                        }
                    }
                }
            }
            return $item['showed'];
        }, ARRAY_FILTER_USE_BOTH);

        // Departure From To
        if (!empty($this->departureStartTimeList)) {
            $validationOk = false;
            foreach ($this->departureStartTimeList as $tripKey => $time) {
                if ($this->departureStartTimeList[$tripKey] != '' && $this->departureEndTimeList[$tripKey] != '' && substr($this->departureStartTimeList[$tripKey], 0, 2) * 60 + substr($this->departureStartTimeList[$tripKey], 3, 2) > substr($this->departureEndTimeList[$tripKey], 0, 2) * 60 + substr($this->departureEndTimeList[$tripKey], 3, 2)) {
                    $this->addError("departureStartTimeList", 'check time range!');
                    $this->addError("departureEndTimeList", 'check time range!');
                } else {
                    $validationOk = true;
                }
            }
            if ($validationOk) {
                $quotes['results'] = array_filter($quotes['results'], function ($item) {
                    foreach ($item['time'] as $tripKey => $time) {
                        if ($time['departure'] && $this->departureStartTimeList[$tripKey] != '' && $this->departureEndTimeList[$tripKey] != '') {
                            $departureDate = new \DateTime($time['departure']);
                            $departureMinutesOfDay = (int)$departureDate->format('i') + (int)$departureDate->format('H') * 60;
                            $departureStartMinutesOfDay = substr($this->departureStartTimeList[$tripKey], 0, 2) * 60 + substr($this->departureStartTimeList[$tripKey], 3, 2);
                            $departureEndMinutesOfDay = substr($this->departureEndTimeList[$tripKey], 0, 2) * 60 + substr($this->departureEndTimeList[$tripKey], 3, 2);
                            if ($departureMinutesOfDay < $departureStartMinutesOfDay || $departureMinutesOfDay > $departureEndMinutesOfDay) {
                                return false;
                            }
                        }
                    }
                    return true;
                }, ARRAY_FILTER_USE_BOTH);
            }
        }

        // Arrival From To
        if (!empty($this->arrivalStartTimeList)) {
            $validationOk = false;
            foreach ($this->arrivalStartTimeList as $tripKey => $time) {
                if ($this->arrivalStartTimeList[$tripKey] != '' && $this->arrivalEndTimeList[$tripKey] != '' && substr($this->arrivalStartTimeList[$tripKey], 0, 2) * 60 + substr($this->arrivalStartTimeList[$tripKey], 3, 2) > substr($this->arrivalEndTimeList[$tripKey], 0, 2) * 60 + substr($this->arrivalEndTimeList[$tripKey], 3, 2)) {
                    $this->addError("arrivalStartTimeList", 'check time range!');
                    $this->addError("arrivalEndTimeList", 'check time range!');
                } else {
                    $validationOk = true;
                }
            }
            if ($validationOk) {
                $quotes['results'] = array_filter($quotes['results'], function ($item) {
                    foreach ($item['time'] as $tripKey => $time) {
                        if ($time['arrival'] && $this->arrivalStartTimeList[$tripKey] != '' && $this->arrivalEndTimeList[$tripKey] != '') {
                            $arrivalDate = new \DateTime($time['arrival']);
                            $arrivalMinutesOfDay = (int)$arrivalDate->format('i') + (int)$arrivalDate->format('H') * 60;
                            $arrivalStartMinutesOfDay = substr($this->arrivalStartTimeList[$tripKey], 0, 2) * 60 + substr($this->arrivalStartTimeList[$tripKey], 3, 2);
                            $arrivalEndMinutesOfDay = substr($this->arrivalEndTimeList[$tripKey], 0, 2) * 60 + substr($this->arrivalEndTimeList[$tripKey], 3, 2);
                            if ($arrivalMinutesOfDay < $arrivalStartMinutesOfDay || $arrivalMinutesOfDay > $arrivalEndMinutesOfDay) {
                                return false;
                            }
                        }
                    }
                    return true;
                }, ARRAY_FILTER_USE_BOTH);
            }
        }

        // Airport Exact Match
        $quotes['results'] = array_filter($quotes['results'], function ($item) use (&$leadFlight) {
            $item['showed'] = true;
            if (!empty($leadFlight)) {
                foreach ($leadFlight as $tripKey => $queryTrip) {
                    if (
                        !empty($item['trips']) && isset($this->airportExactMatch[$tripKey])
                        && $this->airportExactMatch[$tripKey]
                        && !empty($item['trips'][$tripKey]['segments'])
                        && ($item['trips'][$tripKey]['segments'][0]['departureAirportCode'] != $queryTrip->origin
                            || $item['trips'][$tripKey]['segments'][count($item['trips'][$tripKey]['segments']) - 1]['arrivalAirportCode'] != $queryTrip->destination)
                    ) {
                        $item['showed'] = false;
                    }
                }
            }
            return $item['showed'];
        }, ARRAY_FILTER_USE_BOTH);


        return $quotes;
    }
}
