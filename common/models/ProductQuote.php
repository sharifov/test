<?php

namespace common\models;

use common\models\query\ProductQuoteQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;

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
 * @property string $statusName
 * @property string $statusLabel
 * @property string $className
 * @property float $optionAmountSum
 * @property float $totalCalcSum
 * @property ProductQuoteOption[] $productQuoteOptions
 */
class ProductQuote extends \yii\db\ActiveRecord
{

    public const STATUS_PENDING         = 1;
    public const STATUS_IN_PROGRESS     = 2;
    public const STATUS_DONE            = 3;
    public const STATUS_MODIFIED        = 4;
    public const STATUS_DECLINED        = 5;
    public const STATUS_CANCELED        = 6;

    public const STATUS_LIST        = [
        self::STATUS_PENDING        => 'Pending',
        self::STATUS_IN_PROGRESS    => 'In progress',
        self::STATUS_DONE           => 'Done',
        self::STATUS_MODIFIED       => 'Modified',
        self::STATUS_DECLINED       => 'Declined',
        self::STATUS_CANCELED       => 'Canceled',
    ];

    public const STATUS_CLASS_LIST        = [
        self::STATUS_PENDING        => 'warning',
        self::STATUS_IN_PROGRESS    => 'info',
        self::STATUS_DONE           => 'success',
        self::STATUS_MODIFIED       => 'warning',
        self::STATUS_DECLINED       => 'danger',
        self::STATUS_CANCELED       => 'danger',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_quote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pq_id' => 'ID',
            'pq_gid' => 'GID',
            'pq_name' => 'Name',
            'pq_product_id' => 'Product ID',
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
            'pq_owner_user_id' => 'Owner User ID',
            'pq_created_user_id' => 'Created User ID',
            'pq_updated_user_id' => 'Updated User ID',
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

    /**
     * {@inheritdoc}
     * @return ProductQuoteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductQuoteQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function getStatusList(): array
    {
        return self::STATUS_LIST;
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return self::STATUS_LIST[$this->pq_status_id] ?? '';
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return self::STATUS_CLASS_LIST[$this->pq_status_id] ?? '';
    }

    /**
     * @return string
     */
    public function getStatusLabel(): string
    {
        return Html::tag('span', $this->getStatusName(), ['class' => 'badge badge-' . $this->getClassName()]);
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


}
