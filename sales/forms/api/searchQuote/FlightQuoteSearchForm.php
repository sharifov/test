<?php

namespace sales\forms\api\searchQuote;

use common\models\Quote;
use modules\flight\models\FlightQuote;
use sales\helpers\app\AppHelper;
use yii\base\Model;
use yii\helpers\VarDumper;

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
 * @property string $topCriteria
 * @property mixed $rank
 * @property string $departure
 * @property int $departureMin
 * @property int $departureMax
 * @property string $departureStart
 * @property string $departureEnd
 * @property string $arrival
 * @property int $arrivalMin
 * @property int $arrivalMax
 * @property string $arrivalStart
 * @property string $arrivalEnd
 */
class FlightQuoteSearchForm extends Model
{
    public $fareType;

    public $price;

    public $stops;

    public $airlines;

    public $tripDuration;

    public $baggage;

    public $airportChange;

    public $sortBy;

    public $topCriteria;

    public $rank = '0-10';

    public string $departure = '';

    public int $departureMin = 0;

    public int $departureMax = 1440;

    public string $departureStart = '';

    public string $departureEnd = '';

    public string $arrival = '';

    public int $arrivalMin = 0;

    public int $arrivalMax = 1440;

    public string $arrivalStart = '';

    public string $arrivalEnd = '';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                [
                    'fareType', 'airlines', 'tripDuration', 'stops', 'baggage', 'airportChange', 'sortBy',
                    'topCriteria', 'rank', 'departure', 'arrival'
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
    public function getSortBy(): string
    {
        return Quote::getSortAttributeNameById($this->sortBy) ?? Quote::getDefaultSortAttributeName();
    }

    public function getSortType(): int
    {
        return Quote::getSortTypeBySortId($this->sortBy) ?? Quote::getDefaultSortType();
    }

    /**
     * @param array $quotes
     * @return array
     * @throws \Exception
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

        if ($this->topCriteria) {
            $quotes['results'] = AppHelper::filterBySearchInValue($quotes['results'], 'topCriteria', $this->topCriteria);
        }

        if ($this->rank) {
            $ranks = explode('-', $this->rank);
            $quotes['results'] = array_filter($quotes['results'], static function ($item) use ($ranks) {
                $rank = number_format($item['rank'], 1, '.', '');
                return $rank >= $ranks[0] && $rank <= $ranks[1];
            });
        }

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
}
