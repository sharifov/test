<?php

namespace modules\attraction\models;

use common\components\CommunicationService;
use modules\attraction\src\entities\attraction\events\AttractionUpdateRequestEvent;
use modules\attraction\src\entities\attraction\serializer\AttractionSerializer;
use modules\attraction\src\services\attractionQuote\CreateQuoteService;
use modules\attraction\src\useCases\request\update\AttractionUpdateRequestForm;
use modules\product\src\interfaces\Productable;
use modules\product\src\entities\product\Product;
use modules\product\src\interfaces\ProductQuoteService;
use src\entities\EventTrait;
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
 * @property AttractionPax[] $attractionPaxes
 * @property AttractionQuote[] $attractionQuotes
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
    public function getSearchData($quoteKey): array
    {
        //$keyCache = $this->atn_request_hash_key;
        $keyCache = $quoteKey;
        $result = Yii::$app->cache->get($keyCache);

        if ($result === false) {
            $params = [];

            $apiAttractionService = Yii::$app->getModule('attraction')->apiService;

            //$response = $apiAttractionService->getAttractionQuotes($this);
            $response = $apiAttractionService->getProductById($quoteKey);

            if (isset($response['product'])) {
                $result = $response;
                Yii::$app->cache->set($keyCache, $result, 100);
            } else {
                $result = [];
                Yii::error('Not found response[data][attractions]', 'Model:Attraction:getSearchData:apiService');
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
    public static function getAttractionQuoteDataByKey(array $result, int $quoteKey): array
    {
        $quoteList = [];

        if ($quoteKey && isset($result['product']) && $result['product']) {
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

    public function getAdultsCount(): int
    {
        $count = 0;
        if ($this->attractionPaxes) {
            foreach ($this->attractionPaxes as $pax) {
                if ($pax->atnp_type_id == AttractionPax::PAX_LIST_ID[AttractionPax::PAX_ADULT]) {
                    $count++;
                }
            }
        }
        return $count;
    }

    public function getChildCount(): int
    {
        $count = 0;
        if ($this->attractionPaxes) {
            foreach ($this->attractionPaxes as $pax) {
                if ($pax->atnp_type_id == AttractionPax::PAX_LIST_ID[AttractionPax::PAX_CHILD]) {
                    $count++;
                }
            }
        }
        return $count;
    }

    public function getInfantsCount(): int
    {
        $count = 0;
        if ($this->attractionPaxes) {
            foreach ($this->attractionPaxes as $pax) {
                if ($pax->atnp_type_id == AttractionPax::PAX_LIST_ID[AttractionPax::PAX_INFANT]) {
                    $count++;
                }
            }
        }
        return $count;
    }

    public function quoteExist(string $productId, string $date): bool
    {
        $quoteHash = md5($productId . '|' . $date);
        $quotes = $this->attractionQuotes;

        if ($quotes) {
            foreach ($quotes as $quote) {
                if ($quote->atnq_hash_key === $quoteHash) {
                    return true;
                }
            }
        }
        return false;
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

    /**
     * Gets query for [[AttractionPaxes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttractionPaxes()
    {
        return $this->hasMany(AttractionPax::class, ['atnp_atn_id' => 'atn_id']);
    }

    /**
     * Gets query for [[AttractionQuotes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttractionQuotes()
    {
        return $this->hasMany(AttractionQuote::class, ['atnq_attraction_id' => 'atn_id']);
    }

    public function getService(): ProductQuoteService
    {
        return Yii::createObject(CreateQuoteService::class);
    }

    public function getProductName(): string
    {
        return 'Attraction';
    }
}
