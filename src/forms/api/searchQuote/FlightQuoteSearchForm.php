<?php

namespace src\forms\api\searchQuote;

use common\models\Quote;
use src\helpers\app\AppHelper;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Class FlightQuoteSearchForm
 * @package modules\flight\src\useCases\api\searchQuote
 *
 * @property array $fareType
 * @property array $stops
 * @property int $price
 * @property array $airlines
 * @property string|null $tripDuration
 * @property array|null $tripMaxDurationHours
 * @property array|null $tripMaxDurationMinutes
 * @property bool|null $baggage
 * @property bool|null $airportChange
 * @property array|null $airportExactMatch
 * @property array|null $excludeConnectionAirports
 * @property array|null $includeAirports
 * @property string $sortBy
 * @property array $topCriteria
 * @property mixed $rank
 * @property string|null $departure
 * @property string|null $arrival
 * @property array|null $departureStartTimeList
 * @property array|null $departureEndTimeList
 * @property array|null $arrivalStartTimeList
 * @property array|null $arrivalEndTimeList
 * @property int|null $departureMin
 * @property int|null $departureMax
 * @property int|null $arrivalMin
 * @property int|null $arrivalMax
 * @property string|null $departureStart
 * @property string|null $departureEnd
 * @property string|null $arrivalStart
 * @property string|null $arrivalEnd
 * @property int|null $filterIsShown
 */
class FlightQuoteSearchForm extends Model
{
    public $fareType;

    public $price;

    public $stops;

    public $airlines;

    public $tripDuration;

    public $tripMaxDurationHours;

    public $tripMaxDurationMinutes;

    public $baggage;

    public $airportChange;

    public $airportExactMatch;

    public $excludeConnectionAirports;

    public $includeAirports;

    public $sortBy;

    public $topCriteria;

    public $rank = '0-10';

    public $departureStartTimeList;

    public $departureEndTimeList;

    public $arrivalStartTimeList;

    public $arrivalEndTimeList;

    public $filterIsShown = 0;

    public $departure = '';

    public $departureMin = 0;

    public $departureMax = 1440;

    public $departureStart = '';

    public $departureEnd = '';

    public $arrival = '';

    public $arrivalMin = 0;

    public $arrivalMax = 1440;

    public $arrivalStart = '';

    public $arrivalEnd = '';

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
                    'includeAirports', 'topCriteria', 'rank', 'departureStartTimeList','departureEndTimeList',
                    'arrivalStartTimeList', 'arrivalEndTimeList', 'filterIsShown', 'tripDuration', 'departure',
                    'arrival'
                ], 'safe'],
            [
                [
                    'price', 'arrivalMax', 'arrivalMin', 'filterIsShown', 'departureMin', 'departureMax'
                ], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            [['rank', 'departure', 'arrival', 'departureStart', 'departureEnd', 'arrivalStart', 'arrivalEnd'], 'string'],
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

        if (!empty($this->includeAirports)) {
            $quotes['results'] = array_filter($quotes['results'], function ($item) {
                $item['showed'] = false;
                if (!empty($item['trips'])) {
                    foreach ($item['trips'] as $trip) {
                        if (!empty($trip['segments'])) {
                            foreach ($trip['segments'] as $segment) {
                                if (in_array($segment['departureAirportCode'], $this->includeAirports) || in_array($segment['arrivalAirportCode'], $this->includeAirports)) {
                                    $item['showed'] = true;
                                }
                            }
                        }
                    }
                }
                return $item['showed'];
            }, ARRAY_FILTER_USE_BOTH);
        }
        if ($this->topCriteria) {
            if (is_array($this->topCriteria)) {
                $quotes['results'] = AppHelper::filterByArrayContainValues($quotes['results'], 'topCriteria', $this->topCriteria);
            } else {
                $quotes['results'] = AppHelper::filterBySearchInValue($quotes['results'], 'topCriteria', $this->topCriteria);
            }
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


        $departure = explode('-', $this->departure);
        $this->departureStart = (int)trim($departure[0] ?? $this->departureMin);
        $this->departureEnd = (int)trim($departure[1] ?? $this->departureMax);
        if ($this->departure) {
            $quotes['results'] = array_filter($quotes['results'], function ($item) {
                foreach ($item['time'] as $time) {
                    if ($time['departure']) {
                        $departureDate = new \DateTime($time['departure']);
                        $departureMinutesOfDay = (int)$departureDate->format('i') + (int)$departureDate->format('H') * 60;

                        if ($departureMinutesOfDay >= $this->departureStart && $departureMinutesOfDay <= $this->departureEnd) {
                            return true;
                        }
                    }
                }
                return false;
            }, ARRAY_FILTER_USE_BOTH);
        }

        $arrival = explode('-', $this->arrival);
        $this->arrivalStart = (int)trim($arrival[0] ?? $this->arrivalMin);
        $this->arrivalEnd = (int)trim($arrival[1] ?? $this->arrivalMax);
        if ($this->arrival) {
            $quotes['results'] = array_filter($quotes['results'], function ($item) {
                foreach ($item['time'] as $time) {
                    if ($time['arrival']) {
                        $arrivalDate = new \DateTime($time['arrival']);
                        $arrivalMinutesOfDay = (int)$arrivalDate->format('i') + (int)$arrivalDate->format('H') * 60;

                        if ($arrivalMinutesOfDay >= $this->arrivalStart && $arrivalMinutesOfDay <= $this->arrivalEnd) {
                            return true;
                        }
                    }
                }
                return false;
            }, ARRAY_FILTER_USE_BOTH);
        }


        return $quotes;
    }

    public function setSortBy(string $sortBy): FlightQuoteSearchForm
    {
        $this->sortBy = $sortBy;
        return $this;
    }
}
