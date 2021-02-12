<?php

namespace modules\attraction\models;

use common\components\CommunicationService;
use modules\attraction\src\entities\attraction\events\AttractionUpdateRequestEvent;
use modules\attraction\src\useCases\api\searchQuote\AttractionQuoteSearchGuard;
use modules\attraction\src\useCases\request\update\AttractionUpdateRequestForm;
use modules\product\src\interfaces\Productable;
use modules\product\src\entities\product\Product;
use sales\entities\EventTrait;
use yii\db\ActiveQuery;
use Yii;

/**
 * This is the model class for table "attraction".
 *
 * @property int $atn_id
 * @property int|null $atn_product_id
 * @property string|null $atn_date_from
 * @property string|null $atn_date_to
 * @property string|null $atn_destination
 * @property string|null $atn_destination_code
 * @property string|null $atn_request_hash_key
 *
 * @property Product $atnProduct
 */
class Attraction extends \yii\db\ActiveRecord implements Productable
{
    use EventTrait;

    private const DESTINATION_TYPE_COUNTRY  = 0;
    private const DESTINATION_TYPE_CITY     = 1;

    private const DESTINATION_TYPE_LIST = [
        self::DESTINATION_TYPE_COUNTRY  => 'Countries',
        self::DESTINATION_TYPE_CITY     => 'Cities/Zones'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attraction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['atn_product_id'], 'integer'],
            [['atn_product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['atn_product_id' => 'pr_id']],

            [['atn_date_from', 'atn_date_to'], 'safe'],
            [['atn_destination'], 'string', 'max' => 100],
            [['atn_destination_code'], 'string', 'max' => 10],

            [['atn_request_hash_key'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'atn_id' => 'ID',
            'atn_product_id' => 'Product ID',
            'atn_date_from' => 'Date From',
            'atn_date_to' => 'Date To',
            'atn_destination' => 'Destination',
            'ptn_request_hash_key' => 'Request Hash Key',
        ];
    }

    public static function create(int $productId): self
    {
        $attraction = new static();
        $attraction->atn_product_id = $productId;
        return $attraction;
    }

    public function updateRequest(AttractionUpdateRequestForm $form): void
    {
        $this->attributes = $form->attributes;
        $this->recordEvent(new AttractionUpdateRequestEvent($this));
    }

    public function getId(): int
    {
        return $this->atn_id;
    }

    public static function getDestinationList(): array
    {
        return self::DESTINATION_TYPE_LIST;
    }

    public function serialize(): array
    {
        return (new AttractionSerializer($this))->getData();
    }

    public static function findByProduct(int $productId): ?Productable
    {
        return self::find()->byProduct($productId)->limit(1)->one();
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            $request_hash_key = $this->generateRequestHashKey();
            if ($this->atn_request_hash_key !== $request_hash_key) {
                $this->atn_request_hash_key = $request_hash_key;
            }
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    private function generateRequestHashKey(): string
    {
        $keyData[] = $this->atn_destination . '|' . $this->atn_date_from . '|' . $this->atn_date_to . '|' . $this->atn_destination_code;
        $key = implode('|', $keyData);
        return md5($key);
    }

    /**
     * @return array
     */
    public function getSearchData(): array
    {
        $keyCache = $this->atn_request_hash_key;
        $result = Yii::$app->cache->get($keyCache);

        if ($result === false) {
            $params = [];
            /** @var CommunicationService $communication */
            $apiHotelService = Yii::$app->communication;
            //$apiHotelService = Yii::$app->getModule('hotel')->apiService;
            // $service = $hotel->apiService;

            /*$rooms = [];

            if ($this->hotelRooms) {
                foreach ($this->hotelRooms as $room) {
                    $rooms[] = $room->getDataSearch();
                }
            }

            if ($this->ph_max_price_rate) {
                $params['maxRate'] = $this->ph_max_price_rate;
            }

            if ($this->ph_min_price_rate) {
                $params['minRate'] = $this->ph_min_price_rate;
            }*/

            //$response = $apiHotelService->search($this->ph_check_in_date, $this->ph_check_out_date, $this->ph_destination_code, $rooms, $params);
            $response = $apiHotelService->getAttractionQuotes($this);

            if (isset($response['data']['searchSummary'])) {
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
     * @param int $quoteKey
     * @return array
     */
    public static function getHotelQuoteDataByKey(array $result, int $quoteKey): array
    {
        $quoteList = [];
        //$hotelData = self::getHotelDataByCode($result, $hotelCode);

        if ($quoteKey && isset($result['activityGroups']) && $result['activityGroups']) {
            foreach ($result['activityGroups'][0]['activityTiles'] as $quote) {
                $groupKey = (int)($quote['id'] ?? '');
                if (!$groupKey) {
                    continue;
                }
                if ($groupKey === $quoteKey) {
                    $quoteList[$groupKey] = $quote;
                }
            }
        }
        return $quoteList[$quoteKey] ?? [];
    }

    /**
     * Gets query for [[AtnProduct]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAtnProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['pr_id' => 'atn_product_id']);
    }
}
