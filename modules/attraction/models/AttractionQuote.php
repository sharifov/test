<?php

namespace modules\attraction\models;

use modules\attraction\src\serializer\AttractionQuoteSerializer;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\interfaces\Quotable;
use sales\helpers\product\ProductQuoteHelper;
use yii\db\ActiveQuery;
use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "attraction_quote".
 *
 * @property int $atnq_id
 * @property int $atnq_attraction_id
 * @property string|null $atnq_hash_key
 * @property int|null $atnq_product_quote_id
 * @property string|null $atnq_json_response
 * @property string|null $atnq_booking_id
 * @property string|null $atnq_attraction_name
 * @property string|null $atnq_supplier_name
 * @property string|null $atnq_type_name
 *
 * @property Attraction $atnqAttraction
 * @property ProductQuote $atnqProductQuote
 */
class AttractionQuote extends \yii\db\ActiveRecord implements Quotable
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attraction_quote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['atnq_attraction_id'], 'required'],
            [['atnq_attraction_id', 'atnq_product_quote_id'], 'integer'],
            [['atnq_json_response'], 'safe'],
            [['atnq_hash_key'], 'string', 'max' => 32],
            [['atnq_hash_key'], 'unique'],
            [['atnq_attraction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attraction::class, 'targetAttribute' => ['atnq_attraction_id' => 'atn_id']],
            [['atnq_product_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['atnq_product_quote_id' => 'pq_id']],
            [['atnq_booking_id'], 'string', 'max' => 100],
            [['atnq_type_name'], 'string', 'max' => 100],
            [['atnq_attraction_name', 'atnq_supplier_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'atnq_id' => 'ID',
            'atnq_attraction_id' => 'Attraction ID',
            'atnq_hash_key' => 'Hash Key',
            'atnq_product_quote_id' => 'Product Quote ID',
            'atnq_json_response' => 'Json Response',
            'atnq_booking_id' => 'Booking ID',
            'atnq_attraction_name' => 'Attraction Name',
            'atnq_supplier_name' => 'Supplier Name',
            'atnq_type_name' => 'Type Name'
        ];
    }

    public static function getHashKey(array $quote, Attraction $request): string
    {
        return md5(json_encode($quote) . '|' . $request->atn_date_from . '|' . $request->atn_date_to . '|' . time());
    }

    public static function generateGid(): string
    {
        return md5(uniqid('aq', true));
    }

    /**
     * @param array $quoteData
     * @param \modules\attraction\models\Attraction $attractinRequest
     * @param string $currency
     * @return array|AttractionQuote|\yii\db\ActiveRecord|null
     */
    public static function findOrCreateByData(array $quoteData, Attraction $attractionRequest, string $currency = 'USD')
    {
        $aQuote = null;

        if (isset($quoteData['id']) && $quoteId = $quoteData['id']) {
            $totalAmount = 0;
            if (isset($quoteId)) {
                $hashKey = self::getHashKey($quoteData, $attractionRequest);

                $aQuote = self::find()->where([
                    'atnq_attraction_id' => $attractionRequest->atn_id,
                    'atnq_hash_key' => $hashKey
                ])->one();
                $totalAmount = substr($quoteData['leadTicket']['price']['lead']['formatted'], 1);
                //var_dump($totalAmount); die();
                if (!$aQuote) {
                    $prQuote = new ProductQuote();
                    $prQuote->pq_product_id = $attractionRequest->atn_product_id;
                    $prQuote->pq_origin_currency = $currency;
                    $prQuote->pq_client_currency = ProductQuoteHelper::getClientCurrencyCode($attractionRequest->atnProduct);

                    $prQuote->pq_owner_user_id = Yii::$app->user->id;
                    $prQuote->pq_price = (float)$totalAmount;
                    $prQuote->pq_origin_price = (float)$totalAmount;
                    $prQuote->pq_client_price = (float)$totalAmount;
                    $prQuote->pq_status_id = ProductQuoteStatus::PENDING;
                    $prQuote->pq_gid = self::generateGid();
                    $prQuote->pq_service_fee_sum = 0;
                    //$prQuote->pq_client_currency_rate = ProductQuoteHelper::getClientCurrencyRate($hotelRequest->phProduct);
                    $prQuote->pq_origin_currency_rate = 1;
                    $prQuote->pq_name = mb_substr($quoteData['name'], 0, 40);

                    if ($prQuote->save()) {
                        $aQuote = new self();
                        $aQuote->atnq_hash_key = $hashKey;
                        $aQuote->atnq_attraction_id = $attractionRequest->atn_id;
                        $aQuote->atnq_product_quote_id = $prQuote->pq_id;
                        $aQuote->atnq_attraction_name = $quoteData['name'];
                        $aQuote->atnq_supplier_name = $quoteData['supplierName'];
                        $aQuote->atnq_type_name = $quoteData['__typename'];
                        //$aQuote->hq_request_hash = $hotelRequest->ph_request_hash_key;

                        if (!$aQuote->save()) {
                            Yii::error(
                                VarDumper::dumpAsString($aQuote->errors),
                                'Model:AttractionQuote:findOrCreateByData:AttractionQuote:save'
                            );
                        }
                    }
                }
            }
        }

        return $aQuote;
    }

    /**
     * @return bool
     */
    public function isBooking(): bool
    {
        return (!empty($this->atnq_booking_id));
    }

    /**
     * @return bool
     */
    public function isBookable(): bool
    {
        return (ProductQuoteStatus::isBookable($this->atnqProductQuote->pq_status_id) && !$this->isBooking());
    }

    /**
     * Gets query for [[AtnqAttraction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAtnqAttraction(): ActiveQuery
    {
        return $this->hasOne(Attraction::class, ['atn_id' => 'atnq_attraction_id']);
    }

    /**
     * Gets query for [[AtnqProductQuote]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAtnqProductQuote(): ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'atnq_product_quote_id']);
    }

    public static function findByProductQuote(int $productQuoteId): ?Quotable
    {
        return self::findOne(['atnq_product_quote_id' => $productQuoteId]);
    }

    public function serialize(): array
    {
        return (new AttractionQuoteSerializer($this))->getData();
    }

    public function getId(): int
    {
        return $this->atnq_id;
    }

    public function getProcessingFee(): float
    {
        $processingFeeAmount = $this->atnqProductQuote->pqProduct->prType->getProcessingFeeAmount();
        return ProductQuoteHelper::roundPrice($processingFeeAmount);
    }

    public function getSystemMarkUp(): float
    {
        return 0.00;
    }

    public function getAgentMarkUp(): float
    {
        return 0.00;
    }
}
