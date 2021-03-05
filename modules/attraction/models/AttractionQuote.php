<?php

namespace modules\attraction\models;

use modules\attraction\src\serializer\AttractionQuoteSerializer;
use modules\attraction\src\useCases\quote\create\AttractionProductQuoteCreateDto;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productType\ProductType;
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
 * @property string|null $atnq_date
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
            [['atnq_date'], 'string']
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
            'atnq_type_name' => 'Type Name',
            'atnq_date' => 'Date'
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

    public static function findOrCreateByData(
        array $quoteData,
        Attraction $attractionProduct,
        string $date,
        ?int $ownerId,
        string $currency = 'USD'
    ) {
        $aQuote = null;

        if (isset($quoteData['product']) && $quoteId = $quoteData['product']['id']) {
            $totalAmount = 0;
            if (isset($quoteId)) {
                $hashKey = self::getHashKey($quoteData, $attractionProduct);

                $aQuote = self::find()->where([
                    'atnq_attraction_id' => $attractionProduct->atn_id,
                    'atnq_hash_key' => $hashKey
                ])->one();

                //$totalAmount = substr($quoteData['leadTicket']['price']['lead']['formatted'], 1);
                $totalAmount = $quoteData['product']['guidePrice'];

                if (!$aQuote) {
                    $productQuoteDto = new AttractionProductQuoteCreateDto();
                    $productQuoteDto->productId = $attractionProduct->atn_product_id;
                    $productQuoteDto->originCurrency = $currency;
                    $productQuoteDto->clientCurrency = ProductQuoteHelper::getClientCurrencyCode($attractionProduct->atnProduct);
                    $productQuoteDto->ownerUserId = $ownerId;
                    $productQuoteDto->price = (float)$totalAmount;
                    $productQuoteDto->originPrice = (float)$totalAmount;
                    $productQuoteDto->clientPrice = (float)$totalAmount;
                    $productQuoteDto->serviceFeeSum = 0;
//                    $productQuoteDto->clientCurrencyRate = ProductQuoteHelper::getClientCurrencyRate($hotelRequest->phProduct);
                    $productQuoteDto->originCurrencyRate = 1;
                    $productQuoteDto->name = mb_substr($quoteData['product']['name'], 0, 40);

                    $productTypeServiceFee = null;
                    $productType = ProductType::find()->select(['pt_service_fee_percent'])->byAttraction()->asArray()->one();
                    if ($productType && $productType['pt_service_fee_percent']) {
                        $productTypeServiceFee = $productType['pt_service_fee_percent'];
                    }

                    $prQuote = ProductQuote::create($productQuoteDto, $productTypeServiceFee);

                    if ($prQuote->save()) {
                        $aQuote = new self();
                        $aQuote->atnq_hash_key = $hashKey;
                        $aQuote->atnq_attraction_id = $attractionProduct->atn_id;
                        $aQuote->atnq_product_quote_id = $prQuote->pq_id;
                        $aQuote->atnq_attraction_name = $quoteData['product']['name'];
                        $aQuote->atnq_supplier_name = $quoteData['product']['supplierName'];
                        $aQuote->atnq_type_name = $quoteData['product']['__typename'];
                        $aQuote->atnq_json_response = $quoteData;
                        $aQuote->atnq_date = $date;
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
