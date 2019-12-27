<?php

namespace modules\hotel\models;

use common\models\Product;
use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "hotel".
 *
 * @property int $ph_id
 * @property int|null $ph_product_id
 * @property string|null $ph_check_in_dt
 * @property string|null $ph_check_out_dt
 * @property integer|null $ph_zone_code
 * @property integer|null $ph_hotel_code
 * @property string|null $ph_destination_code
 * @property string|null $ph_destination_label
 * @property int|null $ph_min_star_rate
 * @property int|null $ph_max_star_rate
 * @property int|null $ph_max_price_rate
 * @property int|null $ph_min_price_rate
 *
 * @property Product $phProduct
 * @property HotelQuote[] $hotelQuotes
 * @property HotelRoom[] $hotelRooms
 */
class Hotel extends \yii\db\ActiveRecord
{

	private const DESTINATION_TYPE_COUNTRY = 0;
	private const DESTINATION_TYPE_CITY = 1;
	private const DESTINATION_TYPE_HOTEL = 2;

	private const DESTINATION_TYPE_LIST = [
		self::DESTINATION_TYPE_COUNTRY => 'Countries',
		self::DESTINATION_TYPE_CITY => 'Cities',
		self::DESTINATION_TYPE_HOTEL => 'Hotels'
	];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hotel';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ph_product_id', 'ph_min_star_rate', 'ph_max_star_rate', 'ph_max_price_rate', 'ph_min_price_rate', 'ph_zone_code', 'ph_hotel_code'], 'integer'],
            [['ph_check_in_dt', 'ph_check_out_dt'], 'safe'],
            [['ph_zone_code', 'ph_hotel_code'], 'string', 'max' => 11],
            [['ph_destination_code'], 'string', 'max' => 10],
            [['ph_destination_label'], 'string', 'max' => 100],
            [['ph_product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['ph_product_id' => 'pr_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ph_id' => 'ID',
            'ph_product_id' => 'Product ID',
            'ph_check_in_dt' => 'Check In Date',
            'ph_check_out_dt' => 'Check Out Date',
            'ph_zone_code' => 'Zone Code',
            'ph_hotel_code' => 'Hotel Code',
            'ph_destination_code' => 'Destination Code',
            'ph_destination_label' => 'Destination Label',
            'ph_min_star_rate' => 'Min Star Rate',
            'ph_max_star_rate' => 'Max Star Rate',
            'ph_max_price_rate' => 'Max Price Rate',
            'ph_min_price_rate' => 'Min Price Rate',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhProduct()
    {
        return $this->hasOne(Product::class, ['pr_id' => 'ph_product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHotelQuotes()
    {
        return $this->hasMany(HotelQuote::class, ['hq_hotel_id' => 'ph_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHotelRooms()
    {
        return $this->hasMany(HotelRoom::class, ['hr_hotel_id' => 'ph_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\hotel\models\query\HotelQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\hotel\models\query\HotelQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function getSearchData(): array
    {

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

            // $params['maxRate'] = 120;
            //$params['maxHotels'] = 10;

            // MaxRatesPerRoom

            $keyCache = 'hotel_' . $this->ph_id . '_' . implode('_', $params);
            $result = Yii::$app->cache->get($keyCache);

            if($result === false) {
                $response = $apiHotelService->search($this->ph_check_in_dt, $this->ph_check_out_dt, $this->ph_destination_code, $rooms, $params);

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
}
