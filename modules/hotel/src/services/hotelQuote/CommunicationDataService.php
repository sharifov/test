<?php

namespace modules\hotel\src\services\hotelQuote;

use common\models\Project;
use frontend\helpers\JsonHelper;
use modules\hotel\models\HotelQuote;
use modules\hotel\models\HotelQuoteRoom;
use sales\helpers\text\CleanTextHelper;
use yii\helpers\ArrayHelper;

/**
 * Class CommunicationDataService
 */
class CommunicationDataService
{
    public static function hotelConfirmationData(HotelQuote $hotelQuote): array
    {
        self::guard($hotelQuote);

        $hotel = $hotelQuote->hqHotel;
        $hotelList = $hotelQuote->hqHotelList;

        $result['bookingNr'] = $hotelQuote->hq_booking_id;
        $result['description'] = CleanTextHelper::cleanText(self::getDescription($hotelQuote->hq_origin_search_data));
        $result['destination'] = self::getDestinationName($hotelQuote->hq_origin_search_data);
        $result['datetimeCheckIn'] = $hotel->ph_check_in_date;
        $result['datetimeCheckOut'] = $hotel->ph_check_out_date;

        $result['project_key'] = self::getProjectKey($hotelQuote);
        $result['hotel']['name'] = $hotelList->hl_name;
        $result['hotel']['address'] = $hotelList->hl_address;
        $result['hotel']['phone'] = self::getHotelPhone($hotelList->hl_phone_list);
        $result['hotel']['email'] = $hotelList->hl_email;
        $result['hotel']['imgPreview'] = self::getImgSrc($hotelQuote->hq_origin_search_data);

        $result['billing']['currencyCode'] = self::getCurrency($hotelQuote->hq_origin_search_data);

        foreach (HotelQuoteRoom::getRoomsByQuoteId($hotelQuote->hq_id) as $key => $room) {
            $cancellations[0] = [
                'date' => $room->hqr_cancel_from_dt,
                'cost' => $room->hqr_cancel_amount,
            ];
            $prepareRoom = [
                'imgPreview' => '',
                'name' => $room->hqr_room_name,
                'details' => $room->hqr_rate_comments,
                'options' => [],
                'roomGuests' => [
                    'adults' => $room->hqr_adults,
                    'children' => $room->hqr_children,
                    'infants' => 0,
                ],
                'cancellations' => $cancellations,
            ];
            $result['rooms'][$key] = $prepareRoom;
        }
        return $result;
    }

    public static function guard(HotelQuote $hotelQuote): void
    {
        if (!$hotelQuote->hq_booking_id) {
            throw new \RuntimeException('HotelQuote: booking_id is empty');
        }
        if (!$hotelQuote->hq_json_booking) {
            throw new \RuntimeException('HotelQuote: json_booking is empty');
        }
        if (!$hotelQuote->hq_origin_search_data) {
            throw new \RuntimeException('HotelQuote: origin_search_data is empty');
        }
        if (!$hotelQuote->hqHotel) {
            throw new \RuntimeException('Hotel Quote: Hotel not found');
        }
        if (!$hotelQuote->hqHotelList) {
            throw new \RuntimeException('Hotel Quote: HotelList not found');
        }
        if (!$hotelQuote->hotelQuoteRooms) {
            throw new \RuntimeException('Hotel Quote: hotelQuoteRooms is empty');
        }
    }

    public static function getHotelPhone(?string $phoneList): string
    {
        if (!$phoneList || !$list = JsonHelper::decode($phoneList)) {
            return '';
        }
        $list = JsonHelper::decode($phoneList);
        foreach ($list as $key => $value) {
            if (
                ArrayHelper::keyExists('type', $value) &&
                strtoupper($value['type']) === 'PHONEHOTEL' &&
                $number = ArrayHelper::getValue($value, 'number')
            ) {
                return $number;
            }
        }
        return ArrayHelper::getValue($list, '0.number', '');
    }

    public static function getProjectKey(HotelQuote $hotelQuote): string
    {
        if ($project = ArrayHelper::getValue($hotelQuote, 'hqProductQuote.pqProduct.prLead.project')) {
            /** @var Project $project */
            return $project->project_key;
        }
        return '';
    }

    public static function getClientId(HotelQuote $hotelQuote): ?int
    {
        if ($client_id = ArrayHelper::getValue($hotelQuote, 'hqProductQuote.pqProduct.prLead.client_id')) {
            return $client_id;
        }
        return null;
    }

    public static function getLeadId(HotelQuote $hotelQuote): ?int
    {
        if ($lead_id = ArrayHelper::getValue($hotelQuote, 'hqProductQuote.pqProduct.pr_lead_id')) {
            return $lead_id;
        }
        return null;
    }

    private static function getImgSrc(array $originSearchData): string
    {
        if ($img = ArrayHelper::getValue($originSearchData, 'images.0.url')) {
            return 'https://www.gttglobal.com/hotel/img/' . $img;
        }
        return '';
    }

    private static function getDescription(array $originSearchData): string
    {
        return ArrayHelper::getValue($originSearchData, 'description', '');
    }

    private static function getDestinationName(array $originSearchData)
    {
        return ArrayHelper::getValue($originSearchData, 'destinationName', '');
    }

    private static function getCurrency(array $originSearchData)
    {
        return ArrayHelper::getValue($originSearchData, 'currency', 'USD');
    }
}
