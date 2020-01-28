<?php

namespace modules\hotel\models;

use common\models\ProductQuote;
use modules\hotel\models\query\HotelQuoteQuery;
use sales\interfaces\QuoteCommunicationInterface;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "hotel_quote".
 *
 * @property int $hq_id
 * @property int $hq_hotel_id
 * @property string|null $hq_hash_key
 * @property int|null $hq_product_quote_id
 * @property string|null $hq_json_response
 * @property string|null $hq_destination_name
 * @property string $hq_hotel_name
 * @property int|null $hq_hotel_list_id
 * @property string|null $hq_request_hash
 * @property string|null $hq_json_booking
 * @property string|null $hq_booking_id // field "reference" from api response
 *
 * @property Hotel $hqHotel
 * @property HotelList $hqHotelList
 * @property ProductQuote $hqProductQuote
 * @property array $extraData
 * @property HotelQuoteRoom[] $hotelQuoteRooms
 */
class HotelQuote extends ActiveRecord  implements QuoteCommunicationInterface
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'hotel_quote';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['hq_hotel_id', 'hq_hotel_name'], 'required'],
            [['hq_hotel_id', 'hq_product_quote_id', 'hq_hotel_list_id'], 'integer'],
            [['hq_json_response'], 'safe'],
            [['hq_hash_key', 'hq_request_hash'], 'string', 'max' => 32],
            [['hq_destination_name'], 'string', 'max' => 255],
            [['hq_hotel_name'], 'string', 'max' => 200],
            [['hq_hash_key'], 'unique'],
            [['hq_hotel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Hotel::class, 'targetAttribute' => ['hq_hotel_id' => 'ph_id']],
            [['hq_hotel_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => HotelList::class, 'targetAttribute' => ['hq_hotel_list_id' => 'hl_id']],
            [['hq_product_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['hq_product_quote_id' => 'pq_id']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'hq_id' => 'ID',
            'hq_hotel_id' => 'Hotel ID',
            'hq_hash_key' => 'Hash Key',
            'hq_product_quote_id' => 'Product Quote ID',
            'hq_json_response' => 'Json Response',
            'hq_destination_name' => 'Destination Name',
            'hq_hotel_name' => 'Hotel Name',
            'hq_hotel_list_id' => 'Hotel List ID',
            'hq_request_hash' => 'Request Hash',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getHqHotel(): ActiveQuery
    {
        return $this->hasOne(Hotel::class, ['ph_id' => 'hq_hotel_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHqHotelList(): ActiveQuery
    {
        return $this->hasOne(HotelList::class, ['hl_id' => 'hq_hotel_list_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHqProductQuote(): ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'hq_product_quote_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHotelQuoteRooms(): ActiveQuery
    {
        return $this->hasMany(HotelQuoteRoom::class, ['hqr_hotel_quote_id' => 'hq_id']);
    }

    /**
     * @return HotelQuoteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new HotelQuoteQuery(static::class);
    }

    public static function getHashKey(string $roomKey): string
    {
        return md5($roomKey);
    }

    /**
     * @param array $quoteData
     * @param HotelList $hotelModel
     * @param Hotel $hotelRequest
     * @param string $currency
     * @return array|HotelQuote|null
     */
    public static function findOrCreateByData(array $quoteData, HotelList $hotelModel, Hotel $hotelRequest, string $currency = 'USD')
    {
        $hQuote = null;

        if (isset($quoteData['rates']) && $rooms = $quoteData['rates']) {
            if (isset($quoteData['groupKey'])) {
                $hashKey = self::getHashKey($quoteData['groupKey']);

                $hQuote = self::find()->where([
                    'hq_hotel_list_id' => $hotelModel->hl_id,
                    'hq_hotel_id' => $hotelRequest->ph_id,
                    'hq_hash_key' => $hashKey
                ])->one();

                if (!$hQuote) {

                    $totalAmount = 0;
                    $nameArray = [];
                    foreach ($rooms as $room) {
                        $totalAmount += $room['amount'];
                        $nameArray[] = $room['code'] ?? '';
                    }

                    $prQuote = new ProductQuote();
                    $prQuote->pq_product_id = $hotelRequest->ph_product_id;
                    $prQuote->pq_origin_currency = $currency;
                    $prQuote->pq_client_currency = $currency;

                    $prQuote->pq_owner_user_id = Yii::$app->user->id;
                    $prQuote->pq_price = floatval($totalAmount);
                    $prQuote->pq_origin_price = floatval($totalAmount);
                    $prQuote->pq_client_price = floatval($totalAmount);
                    $prQuote->pq_status_id = ProductQuote::STATUS_PENDING;
                    $prQuote->pq_gid = self::generateGid();
                    $prQuote->pq_service_fee_sum = 0;
                    $prQuote->pq_client_currency_rate = 1;
                    $prQuote->pq_origin_currency_rate = 1;
                    $prQuote->pq_name = mb_substr(implode(' & ', $nameArray), 0, 40);

                    if ($prQuote->save()) {

                        $hQuote = new self();
                        $hQuote->hq_hash_key = $hashKey;
                        $hQuote->hq_hotel_id = $hotelRequest->ph_id;
                        $hQuote->hq_hotel_list_id = $hotelModel->hl_id;
                        $hQuote->hq_json_response = json_encode($quoteData);
                        $hQuote->hq_product_quote_id = $prQuote->pq_id;
                        $hQuote->hq_hotel_name = $hotelModel->hl_name;
                        $hQuote->hq_destination_name = $hotelModel->hl_destination_name;
                        $hQuote->hq_request_hash = $hotelRequest->ph_request_hash_key;

                        if (!$hQuote->save()) {
                            Yii::error(VarDumper::dumpAsString($hQuote->errors),
                                'Model:HotelQuote:findOrCreateByData:HotelQuote:save');
                        }
                    }
                }
            }

            if ($hQuote && !$hQuote->hotelQuoteRooms) {
                foreach ($rooms as $room) {
                    $importedHotelRoomIds = [];
                    $importHotelRoomStatus = false;
                    $childrenAges = '';
                    if (array_key_exists('childrenAges', $room) && !empty($room['childrenAges'])) {
                        $childrenAgesArr = explode(',', $room['childrenAges']);
                        sort($childrenAgesArr);
                        $childrenAges = implode(',', $childrenAgesArr);
                    }

                    $qRoom = new HotelQuoteRoom();
                    $qRoom->hqr_hotel_quote_id = $hQuote->hq_id;
                    $qRoom->hqr_adults = $room['adults'] ?? null;
                    $qRoom->hqr_children = $room['children'] ?? null;
                    $qRoom->hqr_children_ages = $childrenAges;
                    $qRoom->hqr_rate_comments_id = $room['rateCommentsId'] ?? null;
                    $qRoom->hqr_rate_comments = $room['rateComments'] ?? null;
                    $qRoom->hqr_type = ($room['type'] == HotelQuoteRoom::TYPE_LIST[HotelQuoteRoom::TYPE_BOOKABLE]) ? HotelQuoteRoom::TYPE_BOOKABLE : HotelQuoteRoom::TYPE_RECHECK;

                    $qRoom->hqr_rooms = $room['rooms'] ?? null;
                    $qRoom->hqr_code = $room['code'] ?? null;
                    $qRoom->hqr_room_name = $room['name'] ?? null;
                    $qRoom->hqr_key = $room['key'] ?? null;
                    $qRoom->hqr_class = $room['class'] ?? null;
                    $qRoom->hqr_payment_type = $room['paymentType'] ?? null;
                    $qRoom->hqr_board_code = $room['boardCode'] ?? null;
                    $qRoom->hqr_board_name = $room['boardName'] ?? null;
                    $qRoom->hqr_amount = $room['amount'] ?? null;
                    $qRoom->hqr_cancel_amount = $room['cancellationPolicies']['amount'] ?? null;
                    $qRoom->hqr_cancel_from_dt = $room['cancellationPolicies']['from'] ?? null;
                    $qRoom->hqr_currency = $currency;

                    if (!$qRoom->save()) {
                        Yii::error(VarDumper::dumpAsString($qRoom->errors),
                            'Model:HotelQuote:findOrCreateByData:HotelQuoteRoom:save');
                    }

                    $hotelRoomsQuery = (new Query())
                        ->select(['hr_id'])
                        ->from(HotelRoom::tableName())
                        ->where(['hr_hotel_id' => $hotelRequest->ph_id]);
                    if (count($importedHotelRoomIds)) {
                        $hotelRoomsQuery->andWhere(['NOT IN', 'hr_id', $importedHotelRoomIds]);
                    }
                    $hotelRooms = $hotelRoomsQuery->all();

                    if (count($hotelRooms)) { // trying to find in hotel_room_pax
                        $hotelRoomPax = new HotelRoomPax();
                        foreach ($hotelRooms as $hotelRoom) {
                            $summaryRoom = $hotelRoomPax->getSummaryByRoom($hotelRoom['hr_id']);
                            if (
                                (int) $summaryRoom['adults'] === (int) $qRoom->hqr_adults &&
                                (int) $summaryRoom['children'] === (int) $qRoom->hqr_children &&
                                $summaryRoom['childrenAges'] == $childrenAges
                            ) { // port info from hotel_room_pax
                                $hotelRoomPaxes = $hotelRoomPax::find()
                                    ->where(['hrp_hotel_room_id' => $hotelRoom['hr_id']])
                                    ->all();

                                foreach ($hotelRoomPaxes as $pax) {
                                    $hotelQuoteRoomPax = new HotelQuoteRoomPax();
                                    $hotelQuoteRoomPax->hqrp_hotel_quote_room_id = $qRoom->hqr_id;
                                    $hotelQuoteRoomPax->hqrp_type_id = $pax->hrp_type_id;
                                    $hotelQuoteRoomPax->hqrp_age = $pax->hrp_age;
                                    $hotelQuoteRoomPax->hqrp_first_name = $pax->hrp_first_name;
                                    $hotelQuoteRoomPax->hqrp_last_name = $pax->hrp_last_name;
                                    $hotelQuoteRoomPax->hqrp_dob = $pax->hrp_dob;
                                    $hotelQuoteRoomPax->save();
                                }
                                array_push($importedHotelRoomIds, $hotelRoom['hr_id']);
                                $importHotelRoomStatus = true;
                            }
                        }
                    }

                    if (!$importHotelRoomStatus) { // if not found in hotel_room_pax
                        if (!empty($qRoom->hqr_adults) && $qRoom->hqr_adults) {
                            for ($i = 0; $i <= $qRoom->hqr_adults; $i++) {
                                $hotelQuoteRoomPax = new HotelQuoteRoomPax();
                                $hotelQuoteRoomPax->hqrp_hotel_quote_room_id = $qRoom->hqr_id;
                                $hotelQuoteRoomPax->hqrp_type_id = $hotelQuoteRoomPax::PAX_TYPE_ADL;
                                $hotelQuoteRoomPax->save();
                            }
                        }

                        if (!empty($qRoom->hqr_children) && $qRoom->hqr_children) {
                            if (isset($childrenAgesArr) && count($childrenAgesArr) === $qRoom->hqr_children) {  // trying to fill age
                                foreach ($childrenAgesArr as $age) {
                                    $hotelQuoteRoomPax = new HotelQuoteRoomPax();
                                    $hotelQuoteRoomPax->hqrp_hotel_quote_room_id = $qRoom->hqr_id;
                                    $hotelQuoteRoomPax->hqrp_type_id = $hotelQuoteRoomPax::PAX_TYPE_CHD;
                                    $hotelQuoteRoomPax->hqrp_age = intval($age);
                                    $hotelQuoteRoomPax->save();
                                }
                            } else { // without age
                                for ($i = 0; $i <= $qRoom->hqr_children; $i++) {
                                    $hotelQuoteRoomPax = new HotelQuoteRoomPax();
                                    $hotelQuoteRoomPax->hqrp_hotel_quote_room_id = $qRoom->hqr_id;
                                    $hotelQuoteRoomPax->hqrp_type_id = $hotelQuoteRoomPax::PAX_TYPE_CHD;
                                    $hotelQuoteRoomPax->save();
                                }
                            }
                        }
                    }
                }
            }
        }

        return $hQuote;
    }

    /**
     * @return string
     */
    public static function generateGid(): string
    {
        return md5(uniqid('hq', true));
    }

    /**
     * @return array
     */
    public function getExtraData(): array
    {
        $data = [];
        $hotelQuoteRoomData = [];
        if ($this->hotelQuoteRooms) {
            foreach ($this->hotelQuoteRooms as $hotelQuoteRoom) {
                $hotelQuoteRoomData[] = $hotelQuoteRoom->extraData;
            }
        }

        $data['hotel'] = $this->hqHotelList ? $this->hqHotelList->extraData : [];
        $data['rooms'] = $hotelQuoteRoomData;
        return $data;
    }

    /**
     * @param HotelQuote $model
     * @return array [bool status, string message]
     */
    public static function book(HotelQuote $model)
    {
        $result = ['status' => 0, 'message' => ''];

        try {
            $rooms = [];
            $client = $model->hqProductQuote->pqProduct->prLead->client;
            if (!empty($model->hq_booking_id)) {
                throw new Exception('Hotel Quote already booked. (BookingId:' . $model->hq_booking_id . ')', 1);
            }
            if (!$hotelQuoteRooms = $model->hotelQuoteRooms) {
                throw new Exception('Hotel Quote Rooms not found. (ID:' . $model->hq_id . ')', 2);
            }

            foreach ($hotelQuoteRooms as $quoteRoom) {
                $rooms[] = [
                    'key' => $quoteRoom->hqr_key,
                    'paxes' => HotelQuoteRoomPax::preparePaxesForBook($quoteRoom->hqr_id)
                ];
            }

            $params = [
                'name' => $client->first_name,
                'surname' => $client->last_name ?: $client->full_name,
                'rooms' => $rooms,
            ];

            $apiHotelService = Yii::$app->getModule('hotel')->apiService;
            $apiResponse = $apiHotelService->book($params);

            if ($apiResponse['status']) {
                $model->hq_booking_id = $apiResponse['data']['booking']['reference'];
                $model->hq_json_booking = $apiResponse['data'];
                $model->save();

                $hqProductQuote = $model->hqProductQuote;
                $hqProductQuote->pq_status_id = $hqProductQuote::STATUS_IN_PROGRESS;  /* TODO: booking status */
                $hqProductQuote->save();

                $result['status'] = 1; // success
                $result['message'] = 'Booking confirmed. (BookingId: ' . $model->hq_booking_id . ')';
            } else {
                $result['message'] = $apiResponse['message'];
            }

        } catch (\Throwable $throwable) {
            Yii::error(VarDumper::dumpAsString($throwable), self::class . ':book:Throwable' );
        }

        return $result;
    }

    /**
     * @param HotelQuote $model
     * @return array
     */
    public static function checkRate(HotelQuote $model)
    {
        $result = ['status' => 0, 'message' => '']; // statuses: 0 error, 1 warning(not all rooms is TYPE_BOOKABLE), 2 success

        try {
            if (!$hotel = $model->hqHotel) {
                throw new Exception('Hotel not found. (ID:' . $model->hq_id . ')', 1);
            }
            if (!$hotelQuoteRooms = $model->hotelQuoteRooms) {
                throw new Exception('Hotel Quote Rooms not found. (ID:' . $model->hq_id . ')', 2);
            }

            $rooms = [];
            foreach ($hotelQuoteRooms as $hotelQuoteRoom) {
                /* @var $hotelQuoteRoom HotelQuoteRoom */
                if ($hotelQuoteRoom->hqr_type === $hotelQuoteRoom::TYPE_BOOKABLE && !empty($hotelQuoteRoom->hqr_rate_comments_id)) {
                    $roomData = [
                        'type' => $hotelQuoteRoom::TYPE_LIST[$hotelQuoteRoom::TYPE_BOOKABLE],
                        'rateCommentsId' => $hotelQuoteRoom->hqr_rate_comments_id,
                    ];
                } else {
                    $roomData = [
                        'key' => $hotelQuoteRoom->hqr_key,
                        'type' => $hotelQuoteRoom::TYPE_LIST[$hotelQuoteRoom::TYPE_RECHECK],
                    ];
                }
                $rooms[] = $roomData;
            }

            $params = [
                'checkIn' => $hotel->ph_check_in_date,
                'rooms' => $rooms,
            ];

            $apiHotelService = Yii::$app->getModule('hotel')->apiService;
            $apiResponse = $apiHotelService->checkRate($params);

            if ($apiResponse['status']) {

                if (isset($apiResponse['data']['hotel']['rooms'])) {
                    foreach ($apiResponse['data']['hotel']['rooms'] as $item) {
                        foreach ($item['rates'] as $room) {
                            if (!$hotelQuoteRoom = HotelQuoteRoom::findOne(['hqr_key' => $room['key']])){
                                throw new Exception('Error: Hotel Quote Room not found. (KEY:' . $room['key'] . ') ', 3);
                            }

                            $hotelQuoteRoom->hqr_rate_comments_id = $room['rateCommentsId'] ?? null;
                            $hotelQuoteRoom->hqr_rate_comments = strip_tags($room['rateComments']) ?? null;
                            $hotelQuoteRoom->hqr_type = ($room['type'] == HotelQuoteRoom::TYPE_LIST[HotelQuoteRoom::TYPE_BOOKABLE]) ? HotelQuoteRoom::TYPE_BOOKABLE : HotelQuoteRoom::TYPE_RECHECK;
                            $hotelQuoteRoom->hqr_cancel_amount = $room['cancellationPolicies']['amount'] ?? null;
                            $hotelQuoteRoom->hqr_cancel_from_dt = $room['cancellationPolicies']['from'] ?? null;
                            $hotelQuoteRoom->save();
                        }
                    }
                } elseif (isset($apiResponse['data']['rateComments']) && !empty($apiResponse['data']['rateComments'])) {
                    HotelQuoteRoom::updateAll(
                        ['hqr_rate_comments' => strip_tags($apiResponse['data']['rateComments'])],
                        ['hqr_hotel_quote_id' => $model->hq_id]
                    );
                }

                $roomsRechecked = HotelQuoteRoom::getRoomsByType($model->hq_id, HotelQuoteRoom::TYPE_RECHECK);

                if (count($roomsRechecked)) {
                    $result['status'] = 1; // warning
                    $result['message'] = 'Not all rooms is bookable.';
                } else {
                    $result['status'] = 2; // success
                    $result['message'] = 'All rooms is bookable.';
                }
            } else {
                $result['message'] = $apiResponse['message'];
            }

        } catch (\Throwable $throwable) {
            $result['status'] = 0;
            $result['message'] = $throwable->getMessage();
            Yii::error(VarDumper::dumpAsString($throwable), self::class . ':checkRate:Throwable' );
        }

        return $result;
    }

    public static function cancelBook(HotelQuote $model)
    {
        $result = ['status' => 0, 'message' => ''];

        try {
            if (empty($model->hq_booking_id)) {
                throw new Exception('Hotel Quote not booked.', 1);
            }

            $apiHotelService = Yii::$app->getModule('hotel')->apiService;
            $apiResponse = $apiHotelService->cancelBook(['bookingId' => $model->hq_booking_id]);

            if ($apiResponse['status']) {

                /* TODO: log ? additional logic */

                $hqProductQuote = $model->hqProductQuote;
                $hqProductQuote->pq_status_id = $hqProductQuote::STATUS_CANCELED;

                $model->hq_booking_id = null;
                $model->hq_json_booking = null;
                $model->save();

                HotelQuoteRoom::updateAll(
                    [
                        'hqr_rate_comments' => null,
                        'hqr_rate_comments_id' => null,
                        'hqr_type' => HotelQuoteRoom::TYPE_RECHECK,
                    ],
                    ['hqr_hotel_quote_id' => $model->hq_id]
                );

                $result['status'] = 1; // success
                $result['message'] = 'Booking canceled.';
            } else {
                $result['message'] = $apiResponse['message'];
            }

        } catch (\Throwable $throwable) {
            $result['status'] = 0;
            $result['message'] = $throwable->getMessage();
            Yii::error(VarDumper::dumpAsString($throwable), self::class . ':cancelBook:Throwable' );
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isBookable() /* TODO: add new  */
    {
        return $this->hqProductQuote->pq_status_id === productQuote::STATUS_PENDING;
    }

}
