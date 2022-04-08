<?php

namespace modules\product\src\entities\productQuote;

use common\models\Currency;
use common\models\Employee;
use modules\flight\models\FlightQuote;
use modules\hotel\models\HotelQuote;
use modules\product\src\entities\productQuote\events\ProductQuoteBookedChangeFlowEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteReplaceEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteStatusChangeEvent;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation;
use modules\product\src\entities\productQuoteData\ProductQuoteData;
use modules\product\src\entities\productQuoteData\ProductQuoteDataKey;
use modules\product\src\entities\productQuoteLead\ProductQuoteLead;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelationQuery;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use modules\cruise\src\entity\cruiseQuote\CruiseQuote;
use modules\attraction\models\AttractionQuote;
use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\order\src\entities\order\events\OrderRecalculateProfitAmountEvent;
use modules\order\src\entities\order\events\OrderRecalculateTotalPriceEvent;
use modules\order\src\entities\order\events\OrderUserProfitUpdateProfitAmountEvent;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\events\ProductQuoteBookedEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteCanceledEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteDeclinedEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteErrorEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteExpiredEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteInProgressEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteCloneCreatedEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteRecalculateChildrenProfitAmountEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteRecalculateProfitAmountEvent;
use modules\product\src\entities\productQuote\serializer\ProductQuoteSerializer;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionsQuery;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\interfaces\Quotable;
use src\dto\product\ProductQuoteDTO;
use src\entities\EventTrait;
use src\helpers\product\ProductQuoteHelper;
use src\entities\serializer\Serializable;
use src\helpers\setting\SettingHelper;
use src\services\CurrencyHelper;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product_quote".
 *
 * @property int $pq_id
 * @property string $pq_gid
 * @property string|null $pq_name
 * @property int $pq_product_id
 * @property int|null $pq_order_id
 * @property string|null $pq_description
 * @property int|null $pq_status_id
 * @property float|null $pq_price
 * @property float|null $pq_origin_price
 * @property float|null $pq_client_price
 * @property float|null $pq_service_fee_sum
 * @property string|null $pq_origin_currency
 * @property string|null $pq_client_currency
 * @property float|null $pq_origin_currency_rate
 * @property float|null $pq_client_currency_rate
 * @property int|null $pq_owner_user_id
 * @property int|null $pq_created_user_id
 * @property int|null $pq_updated_user_id
 * @property string|null $pq_created_dt
 * @property string|null $pq_updated_dt
 * @property float|null $pq_profit_amount
 * @property int|null $pq_clone_id
 * @property float|null $pq_app_markup
 * @property float|null $pq_agent_markup
 * @property float|null $pq_service_fee_percent
 *
 * @property OfferProduct[] $offerProducts
 * @property Offer[] $opOffers
 * @property Order[] $orpOrders
 * @property Currency $pqClientCurrency
 * @property Employee $pqCreatedUser
 * @property Order $pqOrder
 * @property Currency $pqOriginCurrency
 * @property Employee $pqOwnerUser
 * @property Product $pqProduct
 * @property Employee $pqUpdatedUser
 * @property float $optionAmountSum
 * @property float $optionExtraMarkupSum
 * @property float $totalCalcSum
 * @property ProductQuoteOption[] $productQuoteOptions
 * @property ProductQuoteOption[] $productQuoteOptionsActive
 * @property ProductQuote|null $clone
 * @property FlightQuote|null $flightQuote
 * @property HotelQuote|null $hotelQuote
 * @property RentCarQuote|null $rentCarQuote
 * @property CruiseQuote|null $cruiseQuote
 * @property AttractionQuote| $attractionQuote
 * @property ProductQuote[]|null $relates
 * @property ProductQuote|null $relateParent
 * @property ProductQuoteChange|null $productQuoteLastChange
 * @property ProductQuoteChange[] $productQuoteChanges
 * @property ProductQuoteChange[] $productQuoteChangesActive
 * @property ProductQuoteChange[] $productQuoteInvoluntaryChangesActive
 * @property ProductQuoteRefund|null $productQuoteLastRefund
 * @property ProductQuoteRefund[] $productQuoteRefunds
 * @property ProductQuoteRefund[] $productQuoteRefundsActive
 * @property ProductQuoteData|null $productQuoteDataRecommended
 * @property ProductQuoteChangeRelation[]|null $productQuoteChangeRelations
 * @property ProductQuoteChangeRelation|null $productQuoteChangeLastRelation
 * @property ProductQuoteRelation|null $pqRelation
 *
 * @property Quotable|null $childQuote
 * @property string|null $detailsPageUrl
 * @property string|null $diffUrl
 */
class ProductQuote extends \yii\db\ActiveRecord implements Serializable
{
    use EventTrait;

    private $childQuote;

    private ?string $detailsPageUrl = null;
    private ?string $diffUrl = null;

    private ?bool $isQuoteAlternative = null;
    private ?bool $isQuoteOrigin = null;

    public const CHECKOUT_URL_PAGE = 'checkout/quote';

    public static function tableName(): string
    {
        return 'product_quote';
    }

    public function rules(): array
    {
        return [
            [['pq_gid', 'pq_product_id'], 'required'],
            [['pq_product_id', 'pq_order_id', 'pq_owner_user_id', 'pq_created_user_id', 'pq_updated_user_id'], 'integer'],
            [['pq_description'], 'string'],
            [['pq_price', 'pq_origin_price', 'pq_client_price', 'pq_service_fee_sum', 'pq_origin_currency_rate', 'pq_client_currency_rate', 'pq_profit_amount'], 'number'],
            [['pq_created_dt', 'pq_updated_dt'], 'safe'],
            [['pq_gid'], 'string', 'max' => 32],
            [['pq_name'], 'string', 'max' => 40],
            [['pq_origin_currency', 'pq_client_currency'], 'string', 'max' => 3],
            [['pq_origin_currency', 'pq_client_currency'], 'default', 'value' => null],
            [['pq_gid'], 'unique'],
            [['pq_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['pq_client_currency' => 'cur_code']],
            [['pq_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pq_created_user_id' => 'id']],
            [['pq_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['pq_order_id' => 'or_id']],
            [['pq_origin_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['pq_origin_currency' => 'cur_code']],
            [['pq_owner_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pq_owner_user_id' => 'id']],
            [['pq_product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['pq_product_id' => 'pr_id']],
            [['pq_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pq_updated_user_id' => 'id']],

            ['pq_clone_id', 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['pq_clone_id' => 'pq_id'], 'filter' => function ($query) {
                $query->andWhere(['not', ['pq_id' => $this->pq_id]]);
            }],

            ['pq_status_id', 'required'],
            ['pq_status_id', 'integer'],
            ['pq_status_id', 'in', 'range' => array_keys(ProductQuoteStatus::getList())],

            ['pq_app_markup', 'number', /*'min' => 0,*/ 'max' => 99999999],
            ['pq_agent_markup', 'number', /*'min' => 0,*/ 'max' => 99999999],
            ['pq_service_fee_percent', 'number', 'min' => 0, 'max' => 9999],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'pq_id' => 'ID',
            'pq_gid' => 'GID',
            'pq_name' => 'Name',
            'pq_product_id' => 'Product',
            'pqProduct' => 'Product',
            'pq_order_id' => 'Order ID',
            'pq_description' => 'Description',
            'pq_status_id' => 'Status',
            'pq_price' => 'Price',
            'pq_origin_price' => 'Origin Price',
            'pq_client_price' => 'Client Price',
            'pq_service_fee_sum' => 'Service Fee Sum',
            'pq_origin_currency' => 'Origin Currency',
            'pq_client_currency' => 'Client Currency',
            'pq_origin_currency_rate' => 'Origin Currency Rate',
            'pq_client_currency_rate' => 'Client Currency Rate',
            'pq_owner_user_id' => 'Owner User',
            'pq_created_user_id' => 'Created User',
            'pq_updated_user_id' => 'Updated User',
            'pqOwnerUser' => 'Owner User',
            'pqCreatedUser' => 'Created User',
            'pqUpdatedUser' => 'Updated User',
            'pq_created_dt' => 'Created Dt',
            'pq_updated_dt' => 'Updated Dt',
            'pq_clone_id' => 'Clone Id',
            'clone' => 'Clone Id',
            'pq_profit_amount' => 'Profit amount',
            'pq_app_markup' => 'App markup',
            'pq_agent_markup' => 'Agent markup',
            'pq_service_fee_percent' => 'Service fee percent',
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pq_created_dt', 'pq_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pq_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
//            'user' => [
//                'class' => BlameableBehavior::class,
//                'createdByAttribute' => 'pq_created_user_id', //'pq_owner_user_id',
//                'updatedByAttribute' => 'pq_updated_user_id',
//            ],
        ];
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->pq_price                     = $this->pq_price === null ? null : (float) $this->pq_price;
        $this->pq_origin_price              = $this->pq_origin_price === null ? null : (float) $this->pq_origin_price;
        $this->pq_client_price              = $this->pq_client_price === null ? null : (float) $this->pq_client_price;
        $this->pq_service_fee_sum           = $this->pq_service_fee_sum === null ? null : (float) $this->pq_service_fee_sum;
        $this->pq_origin_currency_rate      = $this->pq_origin_currency_rate === null ? null : (float) $this->pq_origin_currency_rate;
        $this->pq_client_currency_rate      = $this->pq_client_currency_rate === null ? null : (float) $this->pq_client_currency_rate;
    }

    public function getChildQuote(): ?Quotable
    {
        if ($this->childQuote !== null) {
            return $this->childQuote;
        }

        $finder = [ProductQuoteClasses::getClass($this->pqProduct->pr_type_id), 'findByProductQuote'];
        $this->childQuote =  $finder($this->pq_id);
        return $this->childQuote;
    }

    public function getClone(): ActiveQuery
    {
        return $this->hasOne(static::class, ['pq_id' => 'pq_clone_id']);
    }

    public function getRelates(): ActiveQuery
    {
        return $this->hasMany(static::class, ['pq_id' => 'pqr_related_pq_id'])
            ->viaTable('product_quote_relation', ['pqr_parent_pq_id' => 'pq_id']);
    }

    public function getRelateParent(): ActiveQuery
    {
        return $this->hasOne(static::class, ['pq_id' => 'pqr_parent_pq_id'])
            ->viaTable('product_quote_relation', ['pqr_related_pq_id' => 'pq_id']);
    }

    public function getPqRelation(): ActiveQuery
    {
        return $this->hasOne(ProductQuoteRelation::class, ['pqr_related_pq_id' => 'pq_id']);
    }

    public function getProductQuoteLastChange(): ActiveQuery
    {
        return $this->hasOne(ProductQuoteChange::class, ['pqc_pq_id' => 'pq_id'])->orderBy(['pqc_pq_id' => SORT_DESC]);
    }

    /**
     * Gets query for [[ProductQuoteChanges]].
     *
     * @return ActiveQuery
     */
    public function getProductQuoteChanges(): ActiveQuery
    {
        return $this->hasMany(ProductQuoteChange::class, ['pqc_pq_id' => 'pq_id']);
    }

    /**
     * Gets query for [[ProductQuoteChanges]].
     *
     * @return ActiveQuery
     */
    public function getProductQuoteChangesActive(): ActiveQuery
    {
        return $this->hasMany(ProductQuoteChange::class, ['pqc_pq_id' => 'pq_id'])->andWhere(['pqc_status_id' => SettingHelper::getActiveQuoteChangeStatuses()]);
    }

    /**
     * Gets query for [[ProductQuoteChanges]].
     *
     * @return ActiveQuery
     */
    public function getProductQuoteInvoluntaryChangesActive(): ActiveQuery
    {
        return $this->hasMany(ProductQuoteChange::class, ['pqc_pq_id' => 'pq_id'])
            ->andWhere(['pqc_status_id' => SettingHelper::getInvoluntaryChangeActiveStatuses()])
            ->andWhere(['pqc_type_id' => ProductQuoteChange::TYPE_RE_PROTECTION]);
    }

    public function getProductQuoteLastRefund(): ActiveQuery
    {
        return $this->hasOne(ProductQuoteRefund::class, ['pqr_product_quote_id' => 'pq_id'])
            ->orderBy(['pqr_id' => SORT_DESC]);
    }

    /**
     * Gets query for [[ProductQuoteRefunds]].
     *
     * @return ActiveQuery
     */
    public function getProductQuoteRefunds(): ActiveQuery
    {
        return $this->hasMany(ProductQuoteRefund::class, ['pqr_product_quote_id' => 'pq_id']);
    }

    /**
     * Gets query for [[ProductQuoteRefunds]].
     *
     * @return ActiveQuery
     */
    public function getProductQuoteRefundsActive(): ActiveQuery
    {
        return $this->hasMany(ProductQuoteRefund::class, ['pqr_product_quote_id' => 'pq_id'])->andWhere(['pqr_status_id' => SettingHelper::getActiveQuoteRefundStatuses()]);
    }

    /**
     * @return ActiveQuery
     */
    public function getOfferProducts(): ActiveQuery
    {
        return $this->hasMany(OfferProduct::class, ['op_product_quote_id' => 'pq_id']);
    }

    public function getFlightQuote()
    {
        return $this->hasOne(FlightQuote::class, ['fq_product_quote_id' => 'pq_id']);
    }

    public function getHotelQuote()
    {
        return $this->hasOne(HotelQuote::class, ['hq_product_quote_id' => 'pq_id']);
    }

    public function getRentCarQuote()
    {
        return $this->hasOne(RentCarQuote::class, ['rcq_product_quote_id' => 'pq_id']);
    }

    public function getCruiseQuote()
    {
        return $this->hasOne(CruiseQuote::class, ['crq_product_quote_id' => 'pq_id']);
    }

    public function getAttractionQuote()
    {
        return $this->hasOne(AttractionQuote::class, ['atnq_product_quote_id' => 'pq_id']);
    }

    /**
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getOpOffers(): ActiveQuery
    {
        return $this->hasMany(Offer::class, ['of_id' => 'op_offer_id'])->viaTable('offer_product', ['op_product_quote_id' => 'pq_id']);
    }

    /**
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getOrpOrders(): ActiveQuery
    {
        return $this->hasMany(Order::class, ['or_id' => 'pq_order_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPqClientCurrency(): ActiveQuery
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'pq_client_currency']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPqCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pq_created_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPqOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['or_id' => 'pq_order_id'])->orderBy(['or_id' => SORT_DESC]);
    }

    /**
     * @return ActiveQuery
     */
    public function getPqOriginCurrency(): ActiveQuery
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'pq_origin_currency']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPqOwnerUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pq_owner_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPqProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['pr_id' => 'pq_product_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPqUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pq_updated_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductQuoteOptions(): ActiveQuery
    {
        return $this->hasMany(ProductQuoteOption::class, ['pqo_product_quote_id' => 'pq_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductQuoteOptionsActive(): ActiveQuery
    {
        return $this->hasMany(ProductQuoteOption::class, ['pqo_product_quote_id' => 'pq_id'])
            ->where(['not', ['pqo_status_id' => ProductQuoteOptionStatus::CANCEL_GROUP]]);
    }

    public function getProductQuoteChangeRelations(): \yii\db\ActiveQuery
    {
        return $this->hasMany(ProductQuoteChangeRelation::class, ['pqcr_pq_id' => 'pq_id']);
    }

    public function getProductQuoteChangeLastRelation(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProductQuoteChangeRelation::class, ['pqcr_pq_id' => 'pq_id'])->orderBy(['pqcr_pqc_id' => SORT_DESC]);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function findByGid(string $gid)
    {
        return self::findOne(['pq_gid' => $gid]);
    }

    /**
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function inNew(?int $creatorId = null, ?string $description = null): void
    {
        $this->setStatusWithEvent(ProductQuoteStatus::NEW, $creatorId, $description);
    }

    /**
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function applied(?int $creatorId = null, ?string $description = null): void
    {
        $this->setStatusWithEvent(ProductQuoteStatus::APPLIED, $creatorId, $description);
    }

    /**
        @deprecated
        use instead method error(?int $creatorId = null, ?string $description = null)
     */
    public function failed(): void
    {
        $this->pq_status_id = ProductQuoteStatus::ERROR;
    }

    /**
     * @return float
     */
    public function getOptionAmountSum(): float
    {
        $sum = 0;
        $options = $this->productQuoteOptions;
        if ($options) {
            foreach ($options as $option) {
                $sum += $option->pqo_price + $option->pqo_extra_markup;
            }
            $sum = round($sum, 2);
        }
        return $sum;
    }

    /**
     * @return float
     */
    public function getTotalCalcSum(): float
    {
        return round($this->optionAmountSum + $this->pq_price, 2);
    }

    /**
     * @return float
     */
    public function getOptionExtraMarkupSum(): float
    {
        $sum = 0;
        if ($options = $this->productQuoteOptionsActive) {
            foreach ($options as $option) {
                $sum += $option->pqo_extra_markup;
            }
        }
        return $sum;
    }

    /**
     * @return float
     */
    public function profitCalc(): float
    {
        $childQuote = $this->getChildQuote();
        $processingFeeAmount = $childQuote ? $childQuote->getProcessingFee() : 0.00; // PFA
        $systemMarkupAmount = $childQuote ? $childQuote->getSystemMarkUp() : 0.00; // MA
        $agentExtraMarkupAmount = $childQuote ? $childQuote->getAgentMarkUp() : 0.00; // EMA agent (pax/room etc.)
        $optionExtraMarkupAmount = $this->getOptionExtraMarkupSum(); // EMA options

        return ($systemMarkupAmount + $agentExtraMarkupAmount + $optionExtraMarkupAmount) - $processingFeeAmount;
    }

    /**
     * @return bool
     */
    public function recalculateProfitAmount(): bool
    {
        $isChanged = false;
//        $profitNew = ProductQuoteHelper::roundPrice($this->profitCalc());
        $profitNew = $this->pq_app_markup + $this->pq_agent_markup;
        $profitOld = $this->pq_profit_amount;

        if ($profitOld !== $profitNew) {
            $this->pq_profit_amount = $profitNew;
            $this->recordEvent(new ProductQuoteRecalculateProfitAmountEvent($this, true));
            $isChanged = true;
        }
        return $isChanged;
    }

    public static function create(ProductQuoteDTO $dto, $serviceFeePercent): ProductQuote
    {
        $quote = new self();

        $quote->pq_gid = self::generateGid();
        $quote->pq_name = $dto->name;
        $quote->pq_product_id = $dto->productId;
        $quote->pq_order_id = $dto->orderId;
        $quote->pq_description = $dto->description;
        $quote->pq_status_id = ProductQuoteStatus::NEW;
        $quote->pq_price = $dto->price;
        $quote->pq_origin_price = $dto->originPrice;
        $quote->pq_client_price = $dto->clientPrice;
        $quote->pq_service_fee_sum = $dto->serviceFeeSum;
        $quote->pq_origin_currency = $dto->originCurrency;
        $quote->pq_client_currency = $dto->clientCurrency;
        $quote->pq_origin_currency_rate = $dto->originCurrencyRate;
        $quote->pq_client_currency_rate = $dto->clientCurrencyRate;
        $quote->pq_owner_user_id = $dto->ownerUserId;
        $quote->pq_created_user_id = $dto->createdUserId;
        $quote->pq_updated_user_id = $dto->updatedUserId;
        $quote->pq_service_fee_percent = $serviceFeePercent ?? 0;

        return $quote;
    }

    public static function clone(
        ProductQuote $quote,
        int $productId,
        ?int $ownerId,
        ?int $creatorId
    ): self {
        $clone = new self();

        $clone->attributes = $quote->attributes;

        $clone->pq_id = null;
        $clone->pq_gid = self::generateGid();
        $clone->pq_product_id = $productId;
        $clone->pq_order_id = null;
        $clone->pq_status_id = ProductQuoteStatus::NEW;
        $clone->pq_owner_user_id = $ownerId;
        $clone->pq_created_user_id = $ownerId;
        $clone->pq_clone_id = $quote->pq_id;
        $clone->recordEvent(new ProductQuoteCloneCreatedEvent(
            $clone,
            null,
            $clone->pq_status_id,
            null,
            ProductQuoteStatusAction::CLONE,
            $clone->pq_owner_user_id,
            $creatorId
        ));

        return $clone;
    }

    public static function copy(ProductQuote $quote, ?int $ownerId, ?int $creatorId): self
    {
        $copy = new self();
        $copy->attributes = $quote->attributes;
        $copy->pq_id = null;
        $copy->pq_gid = self::generateGid();
        $copy->pq_product_id = $quote->pq_product_id;
        $copy->pq_order_id = $quote->pq_order_id;
        $copy->pq_status_id = ProductQuoteStatus::NEW;
        $copy->pq_owner_user_id = $ownerId;
        $copy->pq_created_user_id = $creatorId;
        return $copy;
    }

    public static function replace(ProductQuote $quote): self
    {
        $clone = new self();
        $clone->attributes = $quote->attributes;

        $clone->pq_id = null;
        $clone->pq_gid = self::generateGid();
        $clone->pq_status_id = ProductQuoteStatus::NEW;
        $clone->pq_clone_id = $quote->pq_id;
        $clone->recordEvent(new ProductQuoteReplaceEvent($clone, $quote->pq_id));
        return $clone;
    }

    /**
     * @return string
     */
    public static function generateGid(): string
    {
        return md5(uniqid('fq', true));
    }

    /**
     * @return string
     */
    public function getCheckoutUrlPage(): string
    {
        $url = '#';
        $lead = $this->pqProduct->prLead;
        if ($lead && $lead->project && $lead->project->link) {
            $url = $lead->project->link . '/' . self::CHECKOUT_URL_PAGE . '/' . $this->pq_gid;
        }
        return $url;
    }

    public function serialize(): array
    {
        return (new ProductQuoteSerializer($this))->getData();
    }

    /**
     * @return bool
     */
    public function isApplied(): bool
    {
        return $this->pq_status_id === ProductQuoteStatus::APPLIED;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->pq_status_id === ProductQuoteStatus::NEW;
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->pq_status_id === ProductQuoteStatus::PENDING;
    }

    public function isDeclined(): bool
    {
        return $this->pq_status_id === ProductQuoteStatus::DECLINED;
    }

    public function isCanceled(): bool
    {
        return $this->pq_status_id === ProductQuoteStatus::CANCELED;
    }

    /**
     * @param Currency $currency
     */
    public function recountClientPrice(Currency $currency): void
    {
        $this->pq_client_currency = $currency->cur_code;
        $this->pq_client_currency_rate = $currency->cur_app_rate;
        $this->pq_client_price = ProductQuoteHelper::roundPrice($this->pq_price * $this->pq_client_currency_rate);
    }

    /**
     * @param float $originPrice
     * @param float $price
     * @param float $clientPrice
     * @param float $serviceFeeSum
     */
    public function setQuotePrice(float $originPrice, float $price, float $clientPrice, float $serviceFeeSum)
    {
        $this->pq_origin_price = $originPrice;
        $this->pq_price = $price;
        $this->pq_client_price = $clientPrice;
        $this->pq_service_fee_sum = $serviceFeeSum;
    }

    public function isHotel(): bool
    {
        return $this->pqProduct->isHotel();
    }

    public function isFlight(): bool
    {
        return $this->pqProduct->isFlight();
    }

    public function isAttraction(): bool
    {
        return $this->pqProduct->isAttraction();
    }

    public function isRentCar(): bool
    {
        return $this->pqProduct->isRenTCar();
    }

    public function isCruise(): bool
    {
        return $this->pqProduct->isCruise();
    }

    /**
    * @param int|null $creatorId
    * @param string|null $description
    */
    public function pending(?int $creatorId = null, ?string $description = null): void
    {
        $this->setStatusWithEvent(ProductQuoteStatus::PENDING, $creatorId, $description);
    }

    /**
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function inProgress(?int $creatorId = null, ?string $description = null): void
    {
        $this->recordEvent(
            new ProductQuoteInProgressEvent($this->pq_id, $this->pq_status_id, $description, $this->pq_owner_user_id, $creatorId)
        );
        if ($this->pq_status_id !== ProductQuoteStatus::IN_PROGRESS) {
            $this->setStatus(ProductQuoteStatus::IN_PROGRESS);
            $this->recordEvent(new ProductQuoteRecalculateChildrenProfitAmountEvent($this));
        }
    }

    /**
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function booked(?int $creatorId = null, ?string $description = null): void
    {
        $this->recordEvent(
            new ProductQuoteBookedEvent($this->pq_id, $this->pq_status_id, $description, $this->pq_owner_user_id, $creatorId)
        );

        if ($this->pq_status_id !== ProductQuoteStatus::BOOKED) {
            $this->setStatus(ProductQuoteStatus::BOOKED);
//            $this->recordEvent((new OrderChangeStatusProcessingEvent($this)));
        }
    }

    public function bookedChangeFlow(): void
    {
        if ($this->pq_status_id !== ProductQuoteStatus::BOOKED) {
            $this->recordEvent(
                new ProductQuoteBookedChangeFlowEvent(
                    $this->pq_id,
                    $this->pq_status_id,
                    ProductQuoteStatus::BOOKED,
                    'Exchange API flow',
                    $this->pq_owner_user_id
                )
            );
            $this->setStatus(ProductQuoteStatus::BOOKED);
        }
    }

    /**
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function error(?int $creatorId = null, ?string $description = null): void
    {
        $this->recordEvent(
            new ProductQuoteErrorEvent($this->pq_id, $this->pq_status_id, $description, $this->pq_owner_user_id, $creatorId)
        );
        if ($this->pq_status_id !== ProductQuoteStatus::ERROR) {
            $this->setStatus(ProductQuoteStatus::ERROR);
        }
    }

    /**
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function cancelled(?int $creatorId = null, ?string $description = null): void
    {
        $this->recordEvent(
            new ProductQuoteCanceledEvent($this->pq_id, $this->pq_status_id, $description, $this->pq_owner_user_id, $creatorId)
        );
        if ($this->pq_status_id !== ProductQuoteStatus::CANCELED) {
            $this->setStatus(ProductQuoteStatus::CANCELED);
            $this->recordEvent(new ProductQuoteRecalculateChildrenProfitAmountEvent($this));
        }
    }

    /**
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function declined(?int $creatorId = null, ?string $description = null): void
    {
        $this->recordEvent(
            new ProductQuoteDeclinedEvent($this->pq_id, $this->pq_status_id, $description, $this->pq_owner_user_id, $creatorId)
        );
        if ($this->pq_status_id !== ProductQuoteStatus::DECLINED) {
            $this->setStatus(ProductQuoteStatus::DECLINED);
            $this->recordEvent(new ProductQuoteRecalculateChildrenProfitAmountEvent($this));
        }
    }

    /**
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function expired(?int $creatorId = null, ?string $description = null): void
    {
        $this->recordEvent(
            new ProductQuoteExpiredEvent($this->pq_id, $this->pq_status_id, $description, $this->pq_owner_user_id, $creatorId)
        );
        if ($this->pq_status_id !== ProductQuoteStatus::EXPIRED) {
            $this->setStatus(ProductQuoteStatus::EXPIRED);
            $this->recordEvent(new ProductQuoteRecalculateChildrenProfitAmountEvent($this));
        }
    }

    /**
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function sold(?int $creatorId = null, ?string $description = null): void
    {
        $this->recordEvent(
            new ProductQuoteExpiredEvent($this->pq_id, $this->pq_status_id, $description, $this->pq_owner_user_id, $creatorId)
        );
        if ($this->pq_status_id !== ProductQuoteStatus::SOLD) {
            $this->setStatus(ProductQuoteStatus::SOLD);
        }
    }

    /**
     * @param int|null $status
     */
    private function setStatus(?int $status): void
    {
        if (!array_key_exists($status, ProductQuoteStatus::LIST)) {
            throw new \InvalidArgumentException('Invalid Status');
        }
        ProductQuoteStatus::guard($this->pq_status_id, $status);

        $this->pq_status_id = $status;
    }

    /**
     * @param int|null $status
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function setStatusWithEvent(?int $status, ?int $creatorId = null, ?string $description = null): void
    {
        $this->recordEvent(
            new ProductQuoteStatusChangeEvent(
                $this->pq_id,
                $this->pq_status_id,
                $status,
                $description,
                null,
                $this->pq_owner_user_id,
                $creatorId
                )
            );

        $this->setStatus($status);
    }

    public function prepareRemove(): void
    {
        $this->recordEvent(new ProductQuoteRecalculateChildrenProfitAmountEvent($this));

        if ($childQuote = $this->getChildQuote()) {
            $childQuote->delete();
        }
    }

    public function removeOrderRelation(): void
    {
        if ($this->pq_order_id) {
            $this->recordEvent((new OrderRecalculateProfitAmountEvent([$this->pqOrder])));
        }
        $this->pq_order_id = null;
    }

    public function setOrderRelation(int $orderId): void
    {
        $this->pq_order_id = $orderId;
        $order = $this->pqOrder;
        $this->recordEvent(new OrderRecalculateProfitAmountEvent([$order]));
        $this->recordEvent(new OrderRecalculateTotalPriceEvent($order));
    }

    public function isRelatedWithOrder(): bool
    {
        return !($this->pq_order_id === null);
    }

    public function isTheSameOrder(int $orderId): bool
    {
        return $this->pq_order_id === $orderId;
    }

    public function showClientTotalPriceSumWithOptionsPrice()
    {
        $optionsTotalPrice = ProductQuoteOptionsQuery::getTotalSumClientPriceByQuote($this->pq_id);
        return ProductQuoteHelper::roundPrice((float)$optionsTotalPrice['client_price'] + $this->pq_client_price);
    }

    public function showTotalPriceSumWithOptionsPrice()
    {
        $optionsTotalPrice = ProductQuoteOptionsQuery::getTotalSumPriceByQuote($this->pq_id);
        return ProductQuoteHelper::roundPrice((float)$optionsTotalPrice['total_price'] + $this->pq_price);
    }

    public function isBooked(): bool
    {
        return $this->pq_status_id === ProductQuoteStatus::BOOKED;
    }

    public function isSold(): bool
    {
        return $this->pq_status_id === ProductQuoteStatus::SOLD;
    }

    public function isInProgress(): bool
    {
        return $this->pq_status_id === ProductQuoteStatus::IN_PROGRESS;
    }

    public function isError(): bool
    {
        return $this->pq_status_id === ProductQuoteStatus::ERROR;
    }

    public function isDeletable(): bool
    {
        return ProductQuoteStatus::isDeletable($this->pq_status_id);
    }

    public function isBookable(): bool
    {
        return (ProductQuoteStatus::isBookable($this->pq_status_id) && !$this->isBooked());
    }

    private function calculateServiceFeeSum(): void
    {
        $this->pq_service_fee_sum = CurrencyHelper::roundUp(($this->pq_origin_price + $this->pq_app_markup + $this->pq_agent_markup) * ($this->pq_service_fee_percent / 100));
    }

    private function calculatePrice(): void
    {
        $this->pq_price = $this->pq_origin_price + $this->pq_app_markup + $this->pq_agent_markup + $this->pq_service_fee_sum;
    }

    private function calculateClientPrice(): void
    {
        $this->pq_client_price = CurrencyHelper::convertFromBaseCurrency($this->pq_price, $this->pq_client_currency_rate);
    }

    private function updateProfitAmount(): void
    {
        $this->pq_profit_amount = $this->pq_app_markup + $this->pq_agent_markup;
    }

    public function updatePrices($originPrice, $appMarkup, $agentMarkup): void
    {
        $this->pq_origin_price = $originPrice ?? 0;
        $this->pq_app_markup = $appMarkup ?? 0;
        $this->pq_agent_markup = $agentMarkup ?? 0;

        $this->calculateServiceFeeSum();
        $this->calculatePrice();
        $this->calculateClientPrice();
        $this->updateProfitAmount();
    }

    public function updatePricesC2b(float $originPrice): void
    {
        $this->pq_origin_price = $originPrice;
        $this->pq_app_markup = 0.00;
        $this->pq_agent_markup = 0.00;
        $this->pq_service_fee_sum = 0.00;

        $this->calculatePrice();
        $this->calculateClientPrice();
        $this->updateProfitAmount();
    }

    public function isAlternative(): bool
    {
        return $this->isQuoteAlternative ?? ($this->isQuoteAlternative = ProductQuoteRelationQuery::isRelatedAlternativeQuoteExists($this->pq_id));
    }

    public function isOrigin(): bool
    {
        return $this->isQuoteOrigin ?? ($this->isQuoteOrigin = ProductQuoteRelationQuery::isOriginQuoteExists($this->pq_id));
    }

    public function getQuoteDetailsPageUrl()
    {
        if ($this->detailsPageUrl !== null) {
            return $this->detailsPageUrl;
        }

        $productQuote = \Yii::createObject(ProductQuoteClasses::getClass($this->pqProduct->pr_type_id));
        return $this->detailsPageUrl = $productQuote->getQuoteDetailsPageUrl();
    }

    public function getDiffUrlOriginReprotectionQuotes(): string
    {
        if ($this->diffUrl !== null) {
            return $this->diffUrl;
        }

        $productQuote = \Yii::createObject(ProductQuoteClasses::getClass($this->pqProduct->pr_type_id));
        return $this->diffUrl = $productQuote->getDiffUrlOriginReprotectionQuotes();
    }

    public function isEqual(ProductQuote $quote): bool
    {
        return $this->pq_id === $quote->pq_id;
    }

    public function fields(): array
    {
        $fields = [
            'pq_gid',
            'pq_name',
            'pq_order_id',
            'pq_description',
            'pq_status_id',
            'pq_price',
            'pq_origin_price',
            'pq_client_price',
            'pq_service_fee_sum',
            'pq_origin_currency',
            'pq_client_currency',
        ];
        $fields['pq_status_name'] = function () {
            return ProductQuoteStatus::getName($this->pq_status_id);
        };
        $fields['pq_files'] = function () {
            return (new ProductQuoteFiles())->getList($this);
        };
        if ($quote = $this->getChildQuote()) {
            $fields['data'] = static function () use ($quote) {
                /** @var $quote ActiveRecord */
                return $quote->toArray();
            };
        }
        return $fields;
    }

    /**
     * @return string
     */
    public function getBookingId(): string
    {
        $bookingId = '';
        if ($this->isFlight()) {
            $bookingId = $this->flightQuote->getBookingId();
        }
        return $bookingId;
    }

    public function getLastBookingId(): ?string
    {
        if ($this->isFlight()) {
            return $this->flightQuote->getLastBookingId();
        }
        return null;
    }

    public function getProductQuoteDataRecommended(): ActiveQuery
    {
        return $this->hasOne(ProductQuoteData::class, ['pqd_product_quote_id' => 'pq_id'])->andWhere(['pqd_key' => ProductQuoteDataKey::RECOMMENDED]);
    }

    public function isRecommended(): bool
    {
        return $this->productQuoteDataRecommended ? $this->productQuoteDataRecommended->isRecommended() : false;
    }

    public function isChangeable(): bool
    {
        return in_array($this->pq_status_id, SettingHelper::getProductQuoteChangeableStatuses(), false);
    }

    /**
     * @param int|null $userId
     * @return bool
     */
    public function isOwner(?int $userId): bool
    {
        if ($userId === null) {
            return false;
        }
        return $this->pq_owner_user_id === $userId;
    }

    public function getDifferenceOriginPrice(ProductQuote $originProductQuote): ?float
    {
        /* TODO:: waiting formula */
        $diffPrice = ($this->pq_origin_price - $originProductQuote->pq_origin_price) + $this->pq_agent_markup;
        $diffPrice = $diffPrice < 0 ? 0.00 : $diffPrice;
        return ProductQuoteHelper::roundPrice($diffPrice, 2);
    }

    /**
     * @return int
     */
    public function getProductQuoteOptionsCount(): int
    {
        return $this->productQuoteOptions ? count($this->productQuoteOptions) : 0;
    }
}
