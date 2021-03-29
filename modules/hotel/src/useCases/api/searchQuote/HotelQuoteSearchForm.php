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
 * @property array $categories
 * @property array $selectedCategories
 * @property array $boardsTypes
 * @property array $selectedBoardsTypes
 * @property array $serviceTypes
 * @property array $selectedServiceTypes
 */

class HotelQuoteSearchForm extends Model
{
    public bool $onlyHotels = false;
    public string $priceRange = '';
    public $min;
    public $max;
    public $categories = [];
    public $selectedCategories = [];
    public $boardsTypes = [];
    public $selectedBoardsTypes = [];
    public $serviceTypes = [];
    public $selectedServiceTypes = [];

    public function rules(): array
    {
        return [
            [['onlyHotels', 'priceRange', 'max', 'min', 'categories', 'selectedCategories', 'boardsTypes', 'selectedBoardsTypes',
                'serviceTypes', 'selectedServiceTypes'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'onlyHotels' => 'Show Hotels Only',
            'selectedCategories' => 'Category',
            'selectedBoardsTypes' => 'Board Type',
            'selectedServiceTypes' => 'Service'
        ];
    }

    public function initFilters(array $hotels): void
    {
        if (empty($this->priceRange)) {
            $this->priceRange = self::getRoomsPriceRange($hotels);
        }

        if (empty($this->categories)) {
            $this->categories = self::getCategories($hotels);
        }

        if (empty($this->boardsTypes)) {
            $this->boardsTypes = self::getBoards($hotels);
        }

        if (empty($this->serviceTypes)) {
            $this->serviceTypes = self::getServiceTypes($hotels);
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
            $range = explode('-', $this->priceRange);
            $hotels = AppHelper::filterByRangeMultiLevel($hotels, ['rooms', 'totalAmount'], $range[0], $range[1]);
        }

        if (!empty($this->selectedCategories)) {
            $hotels = self::filterByCategories($hotels, $this->categories, $this->selectedCategories);
        }

        if (!empty($this->selectedBoardsTypes)) {
            $hotels = self::filterByBoard($hotels, $this->boardsTypes, $this->selectedBoardsTypes);
        }

        if (!empty($this->selectedServiceTypes)) {
            $hotels = self::filterByServiceType($hotels, $this->serviceTypes, $this->selectedServiceTypes);
        }

        return $hotels;
    }

    private function filterByBoard(array $hotels, array $boardsTypes, array $selectedBoard): array
    {
        $selectedBoardsNames = [];
        $filteredBoardsTypes = [];

        foreach ($selectedBoard as $identifier) {
            $selectedBoardsNames[] = $boardsTypes[$identifier];
        }

        foreach ($hotels as $key => $hotel) {
            foreach ($hotel['rooms'] as $room) {
                foreach ($room['rates'] as $data) {
                    if (in_array($data['boardName'], $selectedBoardsNames, true)) {
                        $filteredBoardsTypes[$key] = $hotels[$key];
                    }
                }
            }
        }

        return $filteredBoardsTypes;
    }

    private function filterByCategories(array $hotels, array $categories, array $selectedCategories): array
    {
        $selectedCategoriesNames = [];
        $filteredHotels = [];

        foreach ($selectedCategories as $identifier) {
            $selectedCategoriesNames[] = $categories[$identifier];
        }

        foreach ($hotels as $key => $hotel) {
            if (in_array($hotel['categoryName'], $selectedCategoriesNames, true)) {
                $filteredHotels[$key] = $hotels[$key];
            }
        }

        return $filteredHotels;
    }

    private function filterByServiceType(array $hotels, array $categories, array $selectedCategories): array
    {
        $selectedServiceNames = [];
        $filteredHotels = [];

        foreach ($selectedCategories as $identifier) {
            $selectedServiceNames[] = $categories[$identifier];
        }

        foreach ($hotels as $key => $hotel) {
            if (in_array($hotel['serviceType'], $selectedServiceNames, true)) {
                $filteredHotels[$key] = $hotels[$key];
            }
        }

        return $filteredHotels;
    }

    private function getBoards(array $hotels): array
    {
        $boardsTypes = [];

        foreach ($hotels as $hotel) {
            foreach ($hotel['rooms'] as $room) {
                foreach ($room['rates'] as $data) {
                    if (!in_array($data['boardName'], $boardsTypes, true)) {
                        $boardsTypes[] = $data['boardName'];
                    }
                }
            }
        }

        return $boardsTypes;
    }

    private function getCategories(array $hotels): array
    {
        $categories = [];

        foreach ($hotels as $hotel) {
            if (!in_array($hotel['categoryName'], $categories, true)) {
                $categories[] = $hotel['categoryName'];
            }
        }

        return $categories;
    }
    private function getServiceTypes(array $hotels): array
    {
        $serviceTypes = [];

        foreach ($hotels as $hotel) {
            if (!in_array($hotel['serviceType'], $serviceTypes, true)) {
                $serviceTypes[] = $hotel['serviceType'];
            }
        }

        return $serviceTypes;
    }

    private function getRoomsPriceRange(array $hotels): string
    {
        $min = $max = 0;
        foreach ($hotels as $key => $hotel) {
            foreach ($hotel['rooms'] as $room) {
                if ($min == 0) {
                    $min = (int) $room['totalAmount'];
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
