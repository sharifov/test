<?php

namespace modules\hotel\src\helpers;

class HotelApiDataHelper
{
    /**
     * @param string $urlMethod
     * @param array $responseData
     * @return bool
     */
    public function checkDataResponse(string $urlMethod, array $responseData)
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
                if (isset($responseData['booking'])) {
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
    public function prepareDataResponse(string $urlMethod, array $responseData)
    {
        switch ($urlMethod) {
            case 'booking/checkrate_post':
                $result = [
                    'source' => $responseData,
                    'rateComments' => (isset($responseData['rateComments'])) ?: '',
                    'rooms' => ((isset($responseData['hotel']['rooms']))) ? $this->prepareRooms($responseData['hotel']['rooms']) : [],
                ];
                break;
            case 'booking/book_post':
                $result = [
                    'source' => $responseData,
                    'reference' => $responseData['booking']['reference'],
                    'rooms' => ((isset($responseData['booking']['rooms']))) ? $this->prepareRooms($responseData['booking']['rooms']) : [],
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