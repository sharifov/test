<?php

namespace modules\hotel\models;

use modules\hotel\src\entities\hotel\events\HotelUpdateRequestEvent;
use modules\hotel\src\entities\hotel\serializer\HotelSerializer;
use modules\hotel\src\services\hotelQuote\HotelQuoteManageService;
use modules\hotel\src\useCases\request\update\HotelUpdateRequestForm;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\hotel\models\query\HotelQuery;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\interfaces\Productable;
use modules\product\src\interfaces\ProductQuoteService;
use src\auth\Auth;
use src\entities\EventTrait;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "hotel".
 *
 * @property int $ph_id
 * @property int|null $ph_product_id
 * @property string|null $ph_check_in_date
 * @property string|null $ph_check_out_date
 * @property integer|null $ph_zone_code
 * @property integer|null $ph_hotel_code
 * @property string|null $ph_destination_code
 * @property string|null $ph_destination_label
 * @property int|null $ph_min_star_rate
 * @property int|null $ph_max_star_rate
 * @property int|null $ph_max_price_rate
 * @property int|null $ph_min_price_rate
 * @property string|null $ph_request_hash_key
 * @property string|null $ph_holder_name
 * @property string|null $ph_holder_surname
 *
 * @property Product $phProduct
 * @property HotelQuote[] $hotelQuotes
 * @property HotelRoom[] $hotelRooms
 */
class Hotel extends ActiveRecord implements Productable
{
    use EventTrait;

    private const DESTINATION_TYPE_COUNTRY  = 0;
    private const DESTINATION_TYPE_CITY     = 1;
    private const DESTINATION_TYPE_HOTEL    = 2;

    private const DESTINATION_TYPE_LIST = [
        self::DESTINATION_TYPE_COUNTRY  => 'Countries',
        self::DESTINATION_TYPE_CITY     => 'Cities/Zones',
        self::DESTINATION_TYPE_HOTEL    => 'Hotels'
    ];

    public static function create(int $productId): self
    {
        $hotel = new static();
        $hotel->ph_product_id = $productId;
        return $hotel;
    }

    public static function clone(Hotel $hotel, int $productId): self
    {
        $clone = new static();
        $clone->ph_product_id = $productId;
        $clone->ph_check_in_date = $hotel->ph_check_in_date;
        $clone->ph_check_out_date = $hotel->ph_check_out_date;
        $clone->ph_zone_code = $hotel->ph_zone_code;
        $clone->ph_hotel_code = $hotel->ph_hotel_code;
        $clone->ph_destination_code = $hotel->ph_destination_code;
        $clone->ph_destination_label = $hotel->ph_destination_label;
        $clone->ph_min_star_rate = $hotel->ph_min_star_rate;
        $clone->ph_max_star_rate = $hotel->ph_max_star_rate;
        $clone->ph_min_price_rate = $hotel->ph_min_price_rate;
        $clone->ph_max_price_rate = $hotel->ph_max_price_rate;
        $clone->ph_request_hash_key = $hotel->ph_request_hash_key;
        $clone->ph_holder_name = $hotel->ph_holder_name;
        $clone->ph_holder_surname = $hotel->ph_holder_surname;
        return $clone;
    }

    public function updateRequest(HotelUpdateRequestForm $form): void
    {
        $this->attributes = $form->attributes;
        $this->recordEvent(new HotelUpdateRequestEvent($this));
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'hotel';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['ph_product_id', 'ph_min_star_rate', 'ph_max_star_rate', 'ph_max_price_rate', 'ph_min_price_rate', 'ph_zone_code', 'ph_hotel_code'], 'integer'],
            [['ph_check_in_date', 'ph_check_out_date'], 'safe'],
            [['ph_zone_code', 'ph_hotel_code'], 'string', 'max' => 11],
            [['ph_destination_code'], 'string', 'max' => 10],
            [['ph_request_hash_key'], 'string', 'max' => 32],
            [['ph_destination_label'], 'string', 'max' => 100],
            [['ph_product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['ph_product_id' => 'pr_id']],
            [['ph_check_in_date', 'ph_check_out_date'], 'date', 'format' => 'php:Y-m-d'],
            ['ph_check_out_date', 'compare', 'compareAttribute' => 'ph_check_in_date', 'operator' => '>=', 'enableClientValidation' => true],

            ['ph_holder_name', 'string', 'max' => 50],
            ['ph_holder_surname', 'string', 'max' => 50],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'ph_id' => 'ID',
            'ph_product_id' => 'Product ID',
            'ph_check_in_date' => 'Check In Date',
            'ph_check_out_date' => 'Check Out Date',
            'ph_zone_code' => 'Zone Code',
            'ph_hotel_code' => 'Hotel Code',
            'ph_destination_code' => 'Destination Code',
            'ph_destination_label' => 'Destination Label',
            'ph_min_star_rate' => 'Min Star Rate',
            'ph_max_star_rate' => 'Max Star Rate',
            'ph_max_price_rate' => 'Max Price Rate',
            'ph_min_price_rate' => 'Min Price Rate',
            'ph_request_hash_key' => 'Request Hash Key',
            'ph_holder_name' => 'Holder Name',
            'ph_holder_surname' => 'Holder Surname',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPhProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['pr_id' => 'ph_product_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHotelQuotes(): ActiveQuery
    {
        return $this->hasMany(HotelQuote::class, ['hq_hotel_id' => 'ph_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHotelRooms(): ActiveQuery
    {
        return $this->hasMany(HotelRoom::class, ['hr_hotel_id' => 'ph_id']);
    }

    /**
     * @return HotelQuery the active query used by this AR class.
     */
    public static function find(): HotelQuery
    {
        return new HotelQuery(static::class);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            $request_hash_key = $this->generateRequestHashKey();
            if ($this->ph_request_hash_key !== $request_hash_key) {
                $this->ph_request_hash_key = $request_hash_key;
            }
            return true;
        }
        return false;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (isset($changedAttributes['ph_request_hash_key'])) {
            $this->updateInvalidRequestQuotes();
        }
    }

    /**
     * Find invalid request quotes and update status
     */
    public function updateInvalidRequestQuotes(): void
    {
        if ($this->hotelQuotes) {
            foreach ($this->hotelQuotes as $quote) {
                if ($quote->hq_request_hash !== $this->ph_request_hash_key && $quote->hqProductQuote && $quote->hqProductQuote->pq_status_id !== ProductQuoteStatus::DELIVERED) {
                    $creatorId = Auth::id();
                    $description = 'Find invalid request quotes and update status';
                    $quote->hqProductQuote->declined($creatorId, $description);
                    $quote->hqProductQuote->save();
                }
            }
        }
    }


    /**
     * @return string
     */
    private function generateRequestHashKey(): string
    {
        $keyData[] = $this->ph_destination_code . '|' . $this->ph_check_in_date . '|' . $this->ph_check_out_date;
        if ($this->hotelRooms) {
            foreach ($this->hotelRooms as $room) {
                $keyData[] = $room->adtCount . '|' . $room->chdCount;
            }
        }
        $key = implode('|', $keyData);
        return md5($key);
    }

    /**
     * @return array
     */
    public function getSearchData(): array
    {
        $keyCache = $this->ph_request_hash_key;
        $result = Yii::$app->cache->get($keyCache);

        if ($result === false) {
            $params = [];

            $apiHotelService = Yii::$app->getModule('hotel')->apiService;
            // $service = $hotel->apiService;

            $rooms = [];

            if ($this->hotelRooms) {
                foreach ($this->hotelRooms as $room) {
                    $rooms[] = $room->getDataSearch();
                }
            }

            /*$rooms[] = ['rooms' => 1, 'adults' => 1];
            $rooms[] = ['rooms' => 1, 'adults' => 2, 'children' => 2, 'paxes' => [
                ['paxType' => 1, 'age' => 6],
                ['paxType' => 1, 'age' => 14],
            ]];*/

            //            if ($this->ph_max_star_rate) {
            //
            //            }

            if ($this->ph_max_price_rate) {
                $params['maxRate'] = $this->ph_max_price_rate;
            }

            if ($this->ph_min_price_rate) {
                $params['minRate'] = $this->ph_min_price_rate;
            }

            $response = $apiHotelService->search($this->ph_check_in_date, $this->ph_check_out_date, $this->ph_destination_code, $rooms, $params);

            if (isset($response['data']['hotels'])) {
                $result = $response['data'];
                Yii::$app->cache->set($keyCache, $result, 100);
            } else {
                $result = [];
                Yii::error('Not found response[data][hotels]', 'Model:Hotel:getSearchData:apiService');
            }
        }

        return $result;
    }


    /**
     * @param array $result
     * @param int $hotelCode
     * @return array
     */
    public static function getHotelDataByCode(array $result, int $hotelCode): array
    {
        $hotelList = [];
        if (isset($result['hotels']) && $result['hotels']) {
            foreach ($result['hotels'] as $hotel) {
                $hotelList[$hotel['code']] = $hotel;
            }
            //VarDumper::dump($result['hotels']); exit;
        }

        return $hotelList[$hotelCode] ?? [];
    }

    /**
     * @param array $result
     * @param int $hotelCode
     * @param int $quoteKey
     * @return array
     */
    public static function getHotelQuoteDataByKey(array $result, int $hotelCode, int $quoteKey): array
    {
        $quoteList = [];
        $hotelData = self::getHotelDataByCode($result, $hotelCode);

        if ($hotelData && isset($hotelData['rooms']) && $hotelData['rooms']) {
            foreach ($hotelData['rooms'] as $quote) {
                $groupKey = $quote['groupKey'] ?? '';
                if (!$groupKey) {
                    continue;
                }
                $quoteList[$groupKey] = $quote;
            }
        }
        return $quoteList[$quoteKey] ?? [];
    }

    /**
     * @param $quoteKey
     * @return bool
     */
    public function quoteExist($quoteKey): bool
    {
        $quoteHash = md5($quoteKey);
        $quotes = $this->hotelQuotes;

        if ($quotes) {
            foreach ($quotes as $quote) {
                if ($quote->hq_hash_key === $quoteHash) {
                //if ($quote-> === $quoteKey) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public static function getDestinationList(): array
    {
        return self::DESTINATION_TYPE_LIST;
    }

    public function getId(): int
    {
        return $this->ph_id;
    }

    public function serialize(): array
    {
        return (new HotelSerializer($this))->getData();
    }

    public static function findByProduct(int $productId): ?Productable
    {
        return self::find()->byProduct($productId)->limit(1)->one();
    }

    public function getService(): ProductQuoteService
    {
        return Yii::createObject(HotelQuoteManageService::class);
    }

    public function getProductName(): string
    {
        return 'Hotel';
    }
}
