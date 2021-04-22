<?php

namespace modules\attraction\models;

use common\models\Client;
use common\models\Lead;
use common\models\Project;
use modules\attraction\src\entities\attractionQuote\serializer\AttractionQuoteSerializer;
use modules\attraction\src\helpers\AttractionQuoteHelper;
use modules\attraction\src\services\attractionQuote\AttractionQuotePriceCalculator;
use modules\attraction\src\useCases\quote\create\AttractionProductQuoteCreateDto;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\interfaces\ProductDataInterface;
use modules\product\src\interfaces\Quotable;
use sales\helpers\product\ProductQuoteHelper;
use yii\db\ActiveQuery;
use Yii;
use yii\helpers\ArrayHelper;
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
 * @property string|null $atnq_availability_id
 * @property string|null $atnq_availability_product_id
 * @property string|null $atnq_availability_date
 * @property int|null $atnq_availability_is_valid
 * @property float|null $atnq_service_fee_percent
 * @property string|null $atnq_product_details_json
 *
 * @property Attraction $atnqAttraction
 * @property ProductQuote $atnqProductQuote
 * @property AttractionQuoteOptions[] $attractionQuoteOptions
 * @property AttractionQuotePricingCategory[] $attractionQuotePricingCategories
 */
class AttractionQuote extends \yii\db\ActiveRecord implements Quotable, ProductDataInterface
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
            [['atnq_attraction_id', 'atnq_product_quote_id', 'atnq_availability_is_valid'], 'integer'],
            [['atnq_json_response', 'atnq_availability_date', 'atnq_product_details_json'], 'safe'],
            [['atnq_hash_key'], 'string', 'max' => 32],
            [['atnq_hash_key'], 'unique'],
            [['atnq_attraction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attraction::class, 'targetAttribute' => ['atnq_attraction_id' => 'atn_id']],
            [['atnq_product_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['atnq_product_quote_id' => 'pq_id']],
            [['atnq_booking_id'], 'string', 'max' => 100],
            [['atnq_type_name'], 'string', 'max' => 100],
            [['atnq_attraction_name', 'atnq_supplier_name'], 'string', 'max' => 255],
            [['atnq_availability_id', 'atnq_availability_product_id'], 'string', 'max' => 40],
            [['atnq_service_fee_percent'], 'number'],
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
            'atnq_availability_id' => 'Availability ID',
            'atnq_availability_product_id' => 'Availability Product ID',
            'atnq_availability_date' => 'Availability Date',
            'atnq_availability_is_valid' => 'Availability Is Valid',
            'atnq_service_fee_percent' => 'Service Fee Percent',
            'atnq_product_details_json' => 'Product Details Json',
        ];
    }

    public static function getHashKey(array $quote): string
    {
        return md5($quote['productId'] . '|' . $quote['date']);
    }

    public static function generateGid(): string
    {
        return md5(uniqid('aq', true));
    }

    public static function findOrCreateByDataNew(array $quoteData, Attraction $attraction, ?int $ownerId)
    {
        $aQuote = null;

        if (!empty($quoteData['id'])) {
            $hashKey = self::getHashKey($quoteData);

            $aQuote = self::find()->where([
                    'atnq_attraction_id' => $attraction->atn_id,
                    'atnq_hash_key' => $hashKey
                ])->one();

            if (!$aQuote) {
                $productQuoteDto = new AttractionProductQuoteCreateDto();
                $productQuoteDto->productId = $attraction->atn_product_id;
                $productQuoteDto->originCurrency = $quoteData['pricingCategoryList']['currency'];
                $productQuoteDto->clientCurrency = ProductQuoteHelper::getClientCurrencyCode($attraction->atnProduct);
                $productQuoteDto->ownerUserId = $ownerId;
                $productQuoteDto->price =  0;
                $productQuoteDto->originPrice = $quoteData['pricingCategoryList']['priceTotal'];
                $productQuoteDto->clientPrice =  0;
                $productQuoteDto->serviceFeeSum = 0;
                $productQuoteDto->clientCurrencyRate = ProductQuoteHelper::getClientCurrencyRate($attraction->atnProduct);
                $productQuoteDto->originCurrencyRate = 1;
                //$productQuoteDto->name = mb_substr($quoteData['product']['name'], 0, 40);

                $productTypeServiceFee = null;
                $productType = ProductType::find()->select(['pt_service_fee_percent'])->byAttraction()->asArray()->one();
                if ($productType && $productType['pt_service_fee_percent']) {
                    $productTypeServiceFee = $productType['pt_service_fee_percent'];
                }

                $prQuote = ProductQuote::create($productQuoteDto, $productTypeServiceFee);

                if ($prQuote->save()) {
                    $aQuote = new self();
                    $aQuote->atnq_hash_key = $hashKey;
                    $aQuote->atnq_attraction_id = $attraction->atn_id;
                    $aQuote->atnq_product_quote_id = $prQuote->pq_id;
                    //$aQuote->atnq_attraction_name = $quoteData['product']['name'];
                    //$aQuote->atnq_supplier_name = $quoteData['product']['supplierName'];
                    //$aQuote->atnq_type_name = $quoteData['product']['__typename'];
                    $aQuote->atnq_json_response = $quoteData;
                    $aQuote->atnq_availability_date = $quoteData['date'];
                    $aQuote->atnq_availability_id = $quoteData['id'];
                    $aQuote->atnq_availability_product_id = $quoteData['productId'];
                    $aQuote->atnq_service_fee_percent = $productTypeServiceFee;

                    if (!$aQuote->save()) {
                        Yii::error(
                            VarDumper::dumpAsString($aQuote->errors),
                            'Model:AttractionQuote:findOrCreateByData:AttractionQuote:save'
                        );
                    }

                    if ($aQuote) {
                        if (!empty($quoteData['optionList']['nodes'])) {
                            foreach ($quoteData['optionList']['nodes'] as $option) {
                                $quoteAvailabilityOption = new AttractionQuoteOptions();
                                $quoteAvailabilityOption->atqo_attraction_quote_id = $aQuote->atnq_id;
                                $quoteAvailabilityOption->atqo_answered_value = $option['id'];
                                $quoteAvailabilityOption->atqo_label = $option['label'];
                                $quoteAvailabilityOption->atqo_is_answered = (int) $option['isAnswered'];
                                $quoteAvailabilityOption->atqo_answer_formatted_text = $option['answerFormattedText'];

                                if (!$quoteAvailabilityOption->save()) {
                                    Yii::error(
                                        VarDumper::dumpAsString($aQuote->errors),
                                        'Model:AttractionQuote:findOrCreateByData:OptionList:save'
                                    );
                                }
                            }
                        }

                        if (!empty($quoteData['pricingCategoryList']['nodes'])) {
                            foreach ($quoteData['pricingCategoryList']['nodes'] as $category) {
                                if ($category['value'] == 0) {
                                    continue;
                                }
                                $pricingCategory = new AttractionQuotePricingCategory();
                                $pricingCategory->atqpc_attraction_quote_id = $aQuote->atnq_id;
                                $pricingCategory->atqpc_category_id = $category['id'];
                                $pricingCategory->atqpc_label = $category['label'];
                                $pricingCategory->atqpc_min_age = $category['minAge'];
                                $pricingCategory->atqpc_max_age = $category['maxAge'];
                                $pricingCategory->atqpc_min_participants = $category['minParticipants'];
                                $pricingCategory->atqpc_max_participants = $category['maxParticipants'];
                                $pricingCategory->atqpc_quantity = $category['value'];
                                $pricingCategory->atqpc_price = $category['price'];
                                //$pricingCategory->atqpc_currency = $category['currency'];

                                if (!$pricingCategory->save()) {
                                    Yii::error(
                                        VarDumper::dumpAsString($aQuote->errors),
                                        'Model:AttractionQuote:findOrCreateByData:PricingCategoryList:save'
                                    );
                                }
                            }
                        }
                    }
                }
                $prices = (new AttractionQuotePriceCalculator())->calculate($aQuote, $prQuote->pq_origin_currency_rate);
                $prQuote->updatePrices(
                    $prices['originPrice'],
                    $prices['appMarkup'],
                    $prices['agentMarkup']
                );
                $prQuote->save();
            }
        }

        return $aQuote;
    }

    public function getServiceFeePercent(): float
    {
        return $this->atnq_service_fee_percent ?? 0.00;
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

    /**
     * Gets query for [[AttractionQuoteOptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttractionQuoteOptions()
    {
        return $this->hasMany(AttractionQuoteOptions::class, ['atqo_attraction_quote_id' => 'atnq_id']);
    }

    /**
     * Gets query for [[AttractionQuotePricingCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttractionQuotePricingCategories()
    {
        return $this->hasMany(AttractionQuotePricingCategory::class, ['atqpc_attraction_quote_id' => 'atnq_id']);
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

    public function getProject(): Project
    {
        if ($project = ArrayHelper::getValue($this, 'atnqProductQuote.pqProduct.project')) {
            return $project;
        }
        if ($project = ArrayHelper::getValue($this, 'atnqProductQuote.pqProduct.prLead.project')) {
            return $project;
        }
        throw new \DomainException('AttractionQuote not related to project');
    }

    public function getLead(): ?Lead
    {
        return ArrayHelper::getValue($this, 'atnqProductQuote.pqProduct.prLead');
    }

    public function getClient(): ?Client
    {
        return ArrayHelper::getValue($this, 'atnqProductQuote.pqProduct.prLead.client');
    }

    public function getOrder(): ?Order
    {
        return ArrayHelper::getValue($this, 'atnqProductQuote.pqOrder');
    }
}
