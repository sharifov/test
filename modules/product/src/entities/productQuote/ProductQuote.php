<?php

namespace modules\product\src\entities\productQuote;

use common\models\Currency;
use common\models\Employee;
use modules\flight\models\FlightQuote;
use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\order\src\entities\order\events\OrderRecalculateProfitAmountEvent;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\events\ProductQuoteBookedEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteCalculateUserProfitEvent;
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
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use modules\product\src\interfaces\Quotable;
use sales\dto\product\ProductQuoteDTO;
use sales\entities\EventTrait;
use sales\helpers\product\ProductQuoteHelper;
use sales\entities\serializer\Serializable;
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
 *
 * @property Quotable|null $childQuote
 */
class ProductQuote extends \yii\db\ActiveRecord implements Serializable
{
    use EventTrait;

    private $childQuote;

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
            [['pq_gid'], 'unique'],
            [['pq_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['pq_client_currency' => 'cur_code']],
            [['pq_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pq_created_user_id' => 'id']],
            [['pq_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['pq_order_id' => 'or_id']],
            [['pq_origin_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['pq_origin_currency' => 'cur_code']],
            [['pq_owner_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pq_owner_user_id' => 'id']],
            [['pq_product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['pq_product_id' => 'pr_id']],
            [['pq_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pq_updated_user_id' => 'id']],

            ['pq_clone_id', 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['pq_clone_id' => 'pq_id'], 'filter' => function($query) {
                $query->andWhere(['not', ['pq_id' => $this->pq_id]]);
            }],

            ['pq_status_id', 'required'],
            ['pq_status_id', 'integer'],
            ['pq_status_id', 'in', 'range' => array_keys(ProductQuoteStatus::getList())],
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
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'pq_created_user_id', //'pq_owner_user_id',
                'updatedByAttribute' => 'pq_updated_user_id',
            ],
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
        return $this->hasOne(Order::class, ['or_id' => 'pq_order_id']);
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

    public static function find(): Scopes
    {
        return new Scopes(static::class);
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
                $sum += $option->pqo_price;
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
        $profitNew = ProductQuoteHelper::roundPrice($this->profitCalc());
        $profitOld = ProductQuoteHelper::roundPrice((float) $this->pq_profit_amount);

        if ($profitOld !== $profitNew) {
            $this->pq_profit_amount = $profitNew;
            $this->recordEvent(new ProductQuoteRecalculateProfitAmountEvent($this));
            $isChanged = true;
        }
        return $isChanged;
    }

	/**
	 * @param ProductQuoteDTO $dto
	 * @return ProductQuote
	 */
    public static function create(ProductQuoteDTO $dto): ProductQuote
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

		return $quote;
	}

    public static function clone(
        ProductQuote $quote,
        int $productId,
        ?int $ownerId,
        ?int $creatorId
    ): self
    {
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

	/**
	 * @return string
	 */
	private static function generateGid(): string
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
		if($lead && $lead->project && $lead->project->link) {
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

	public function isDeclined(): bool
	{
		return $this->pq_status_id === ProductQuoteStatus::DECLINED;
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
			$this->recordEvent((new ProductQuoteCalculateUserProfitEvent($this)));
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

    public function prepareRemove(): void
    {
        $this->recordEvent(new ProductQuoteRecalculateChildrenProfitAmountEvent($this));

        if ($childQuote = $this->getChildQuote()){
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
		$this->recordEvent((new OrderRecalculateProfitAmountEvent([$this->pqOrder])));
	}

	public function isRelatedWithOrder(): bool
	{
		return !($this->pq_order_id === null);
	}

	public function isTheSameOrder(int $orderId): bool
	{
		return $this->pq_order_id === $orderId;
	}
}