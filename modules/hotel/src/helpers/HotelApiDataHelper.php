<?php

namespace modules\hotel\src\helpers;

/**
 * Class HotelApiDataHelper
 * @package modules\hotel\src\helpers
 */
class HotelApiDataHelper
{
    /**
     * @param string $urlMethod
     * @param array $responseData
     * @return bool
     */
    public function checkDataResponse(string $urlMethod, array $responseData): bool
    {
        $result = false;
        switch ($urlMethod) {
            case 'booking/checkrate_post':
                if (isset($responseData['hotel']['rooms']) || isset($responseData['rateComments'])) {
                    $result = true;
                }
                break;
            case 'booking/book_post':
                if (isset($responseData['booking']['reference'])) {
                    $result = true;
                }
                break;
            case 'booking/book_delete':
                if (isset($responseData['booking']['status']) && strtoupper($responseData['booking']['status']) === 'CANCELLED') {
                    $result = true;
                }
                break;
        }
        return $result;
	}

    /**
     * @param string $urlMethod
     * @param array $responseData
     * @return array
     */
    public function prepareDataResponse(string $urlMethod, array $responseData): array
    {
        switch ($urlMethod) {
            case 'booking/checkrate_post':
                $result = [
                    'source' => $responseData,
                    'rateComments' => $responseData['rateComments'] ?? '',
                    'rooms' => ((isset($responseData['hotel']['rooms']))) ? $this->prepareRooms($responseData['hotel']['rooms']) : [],
                ];
                break;
            case 'booking/book_post':
                $result = [
                    'source' => $responseData,
                    'reference' => $responseData['booking']['reference'],
                ];
                break;
            case 'booking/book_delete':
                $result = [];
                break;
            default:
                $result = [];
        }
        return $result;
    }

    /**
     * @param array $responseRooms
     * @return array
     */
    public function prepareRooms(array $responseRooms)
    {
        $result = [];
        foreach ($responseRooms as $item) {
            foreach ($item['rates'] as $room) {
                $result[] = $room;
            }
        }
        return $result;
    }
}