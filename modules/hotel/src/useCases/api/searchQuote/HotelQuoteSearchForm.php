<?php

namespace modules\hotel\src\useCases\api\searchQuote;

use sales\helpers\app\AppHelper;
use yii\base\Model;
use yii\helpers\VarDumper;

/**
 * Class HotelQuoteSearchForm
 * @package modules\hotel\src\useCases\api\searchQuote
 *
 * @property bool $onlyHotels
 * @property string $priceRange
 * @property int $min
 * @property int $max
 */

class HotelQuoteSearchForm extends Model
{
    public bool $onlyHotels = false;
    public string $priceRange = '';
    public $min;
    public $max;

    public function rules(): array
    {
        return [
            [['onlyHotels', 'priceRange', 'max', 'min'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'onlyHotels' => 'Show Hotels Only'
        ];
    }

    public function initFilters(array $hotels): void
    {
        if (empty($this->priceRange)) {
            $this->priceRange = self::getRoomsPriceRange($hotels);
        }
    }

    /**
     * @param array $quotes
     * @return array
     */
    public function applyFilters(array $hotels): array
    {
        if ($this->onlyHotels) {
            $hotels = AppHelper::filterByValueMultiLevel($hotels, ['accomodationType', 'code'], 'HOTEL');
        }

        if (!empty($this->priceRange)) {
            //VarDumper::dump($hotels[0], 10, true); die();
            $range = explode('-', $this->priceRange);
            $hotels = AppHelper::filterByRangeMultiLevel($hotels, ['rooms', 'totalAmount'], $range[0], $range[1]);
        }

        return $hotels;
    }

    private function getRoomsPriceRange(array $hotels): string
    {
        $min = $max = 0;
        foreach ($hotels as $key => $hotel) {
            foreach ($hotel['rooms'] as $room) {
                if ($key == 0) {
                    $min = $max = (int) $room['totalAmount'];
                }
                $price = (int) $room['totalAmount'];
                if ($price <= $min) {
                    $min = $price ;
                }

                if ($price > $max) {
                    $max =  $price ;
                }
            }
        }
        $this->min = $min;
        $this->max = $max;
        return $min . '-' . $max;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getFilters(): array
    {
        return [ (new \ReflectionClass($this))->getShortName()  => $this->toArray()];
    }
}
