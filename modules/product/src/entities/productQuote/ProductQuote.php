<?php

namespace modules\product\src\entities\productQuote;

use common\models\Currency;
use common\models\Employee;
use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\orderProduct\OrderProduct;
use modules\product\src\entities\productQuote\events\ProductQuoteBookedEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteErrorEvent;
use modules\product\src\entities\productQuote\events\ProductQuoteInProgressEvent;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productType\ProductType;
use modules\flight\models\FlightQuote;
use modules\flight\src\useCases\flightQuote\create\ProductQuoteCreateDTO;
use modules\hotel\models\HotelQuote;
use modules\product\src\entities\product\Product;
use sales\entities\EventTrait;
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
 *
 * @property OfferProduct[] $offerProducts
 * @property Offer[] $opOffers
 * @property OrderProduct[] $orderProducts
 * @property Order[] $orpOrders
 * @property Currency $pqClientCurrency
 * @property Employee $pqCreatedUser
 * @property Order $pqOrder
 * @property Currency $pqOriginCurrency
 * @property Employee $pqOwnerUser
 * @property Product $pqProduct
 * @property Employee $pqUpdatedUser
 * @property float $optionAmountSum
 * @property float $totalCalcSum
 * @property array $extraData
 * @property ProductQuoteOption[] $productQuoteOptions
 */
class ProductQuote extends \yii\db\ActiveRecord
{
    use EventTrait;

	public const CHECKOUT_URL_PAGE = 'checkout/quote';

	public static function tableName(): string
    {
        return 'product_quote';
    }

    public function rules(): array
    {
        return [
            [['pq_gid', 'pq_product_id'], 'required'],
            [['pq_product_id', 'pq_order_id', 'pq_status_id', 'pq_owner_user_id', 'pq_created_user_id', 'pq_updated_user_id'], 'integer'],
            [['pq_description'], 'string'],
            [['pq_price', 'pq_origin_price', 'pq_client_price', 'pq_service_fee_sum', 'pq_origin_currency_rate', 'pq_client_currency_rate'], 'number'],
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
        ];
    }

    /**
     * @return array
     */
    public function extraFields(): array
    {
        return [
//            'pq_id',
            'pq_gid',
            'pq_name',
//            'pq_product_id',
            'pq_order_id',
            'pq_description',
            'pq_status_id',
            'pq_price',
            'pq_origin_price',
            'pq_client_price',
            'pq_service_fee_sum',
            'pq_origin_currency',
            'pq_client_currency',
//            'pq_origin_currency_rate',
//            'pq_client_currency_rate',
//            'pq_owner_user_id',
//            'pq_created_user_id',
//            'pq_updated_user_id',
//            'pq_created_dt',
//            'pq_updated_dt',
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
            'pq_status_id' => 'Status ID',
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

    /**
     * @return ActiveQuery
     */
    public function getOfferProducts(): ActiveQuery
    {
        return $this->hasMany(OfferProduct::class, ['op_product_quote_id' => 'pq_id']);
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
     */
    public function getOrderProducts(): ActiveQuery
    {
        return $this->hasMany(OrderProduct::class, ['orp_product_quote_id' => 'pq_id']);
    }

    /**
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getOrpOrders(): ActiveQuery
    {
        return $this->hasMany(Order::class, ['or_id' => 'orp_order_id'])->viaTable('order_product', ['orp_product_quote_id' => 'pq_id']);
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


//    public function getQuoteItem()
//    {
//        $quote = null;
//        if ($this->pqProduct->pr_type_id === ProductType::PRODUCT_FLIGHT) {
//            $quote = FlightQuote::find()->where(['fq_product_quote_id' => $this->pq_id])->one();
//        } elseif ($this->pqProduct->pr_type_id === ProductType::PRODUCT_HOTEL) {
//            $quote = HotelQuote::find()->where(['hq_product_quote_id' => $this->pq_id])->one();
//        }
//
//        return $quote;
//    }

    /**
     * @return array
     */
    public function getExtraData(): array
    {
        $quoteData = array_intersect_key($this->attributes, array_flip($this->extraFields()));

        if ($this->pqProduct->pr_type_id === ProductType::PRODUCT_FLIGHT) {
            $quote = FlightQuote::find()->where(['fq_product_quote_id' => $this->pq_id])->one();
            if ($quote) {
                $quoteData['data'] = $quote->extraData;
            }
        } elseif ($this->pqProduct->pr_type_id === ProductType::PRODUCT_HOTEL) {
            $quote = HotelQuote::find()->where(['hq_product_quote_id' => $this->pq_id])->one();
            if ($quote) {
                $quoteData['data'] = $quote->extraData;
            }
        }

        //$quoteData['attr'] = array_intersect_key($this->attributes, array_flip($this->extraFields()));

        return $quoteData;
    }

	/**
	 * @param ProductQuoteCreateDTO $dto
	 * @return ProductQuote
	 */
    public static function create(ProductQuoteCreateDTO $dto): ProductQuote
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

    /**
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function inProgress(?int $creatorId, ?string $description = ''): void
    {
        $this->recordEvent(
            new ProductQuoteInProgressEvent($this->pq_id, $this->pq_status_id, $description, $this->pq_owner_user_id, $creatorId)
        );
        if ($this->pq_status_id !== ProductQuoteStatus::IN_PROGRESS) {
            $this->setStatus(ProductQuoteStatus::IN_PROGRESS);
        }
    }

    /**
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function booked(?int $creatorId, ?string $description = ''): void
    {
        $this->recordEvent(
            new ProductQuoteBookedEvent($this->pq_id, $this->pq_status_id, $description, $this->pq_owner_user_id, $creatorId)
        );
        if ($this->pq_status_id !== ProductQuoteStatus::BOOKED) {
            $this->setStatus(ProductQuoteStatus::BOOKED);
        }
    }

    /**
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function error(?int $creatorId, ?string $description = ''): void
    {
       $this->recordEvent(
            new ProductQuoteErrorEvent($this->pq_id, $this->pq_status_id, $description, $this->pq_owner_user_id, $creatorId)
        );
       if ($this->pq_status_id !== ProductQuoteStatus::ERROR) {
            $this->setStatus(ProductQuoteStatus::ERROR);
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
}
