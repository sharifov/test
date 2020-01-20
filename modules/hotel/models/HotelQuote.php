<?php

namespace modules\hotel\models;

use common\models\ProductQuote;
use modules\hotel\models\query\HotelQuoteQuery;
use sales\interfaces\QuoteCommunicationInterface;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
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
 *
 * @property Hotel $hqHotel
 * @property HotelList $hqHotelList
 * @property ProductQuote $hqProductQuote
 * @property array $extraData
 * @property HotelQuoteRoom[] $hotelQuoteRooms
 */
class HotelQuote extends ActiveRecord  implements QuoteCommunicationInterface
{
    public const
        STATUS_NEW = 1,
        STATUS_PENDING = 2,
        STATUS_BOOKED = 3,
        STATUS_CANCELED = 4;

    public const STATUS_LIST = [
        self::STATUS_NEW => 'New',
        self::STATUS_PENDING => 'Pending',
        self::STATUS_BOOKED => 'Booked',
        self::STATUS_CANCELED => 'Canceled',
    ];

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
                    $qRoom = new HotelQuoteRoom();
                    $qRoom->hqr_hotel_quote_id = $hQuote->hq_id;
                    $qRoom->hqr_adults = $room['adults'] ?? null;
                    $qRoom->hqr_children = $room['children'] ?? null;
                    //childrenAges

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

    /* TODO: */
    public static function book()
    {
        return true;
    }

    /* TODO: */
    public static function cancelBook()
    {
        return true;
    }
}
