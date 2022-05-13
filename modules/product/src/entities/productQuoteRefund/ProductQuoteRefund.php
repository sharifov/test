<?php

namespace modules\product\src\entities\productQuoteRefund;

use common\components\validators\CheckJsonValidator;
use common\models\Currency;
use common\models\Employee;
use common\models\query\CurrencyQuery;
use modules\order\src\entities\orderRefund\OrderRefund;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefund;
use modules\product\src\entities\productQuoteRefund\serializer\ProductQuoteRefundSerializer;
use src\entities\cases\Cases;
use src\entities\serializer\Serializable;
use src\services\CurrencyHelper;
use src\traits\FieldsTrait;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "product_quote_refund".
 *
 * @property int $pqr_id
 * @property int $pqr_order_refund_id
 * @property int $pqr_product_quote_id [int]
 * @property float|null $pqr_selling_price
 * @property float|null $pqr_penalty_amount
 * @property float|null $pqr_processing_fee_amount
 * @property float|null $pqr_refund_amount
 * @property int|null $pqr_status_id
 * @property string|null $pqr_client_currency
 * @property float|null $pqr_client_currency_rate
 * @property float|null $pqr_client_selling_price
 * @property float|null $pqr_client_refund_amount
 * @property int|null $pqr_created_user_id
 * @property int|null $pqr_updated_user_id
 * @property string|null $pqr_created_dt
 * @property string|null $pqr_updated_dt
 * @property string|null $pqr_expiration_dt
 * @property int|null $pqr_case_id
 * @property string $pqr_type_id [tinyint unsigned]
 *
 * @property Currency $clientCurrency
 * @property Employee $createdUser
 * @property OrderRefund $orderRefund
 * @property Employee $updatedUser
 * @property ProductQuote $productQuote
 * @property ProductQuoteObjectRefund[] $productQuoteObjectRefunds
 * @property ProductQuoteOptionRefund[] $productQuoteOptionRefunds
 * @property Cases $case
 * @property string $pqr_data_json [json]
 * @property string $pqr_gid [varchar(32)]
 * @property string $pqr_cid [varchar(32)]
 * @property string $pqr_client_penalty_amount [decimal(8,2)]
 * @property string $pqr_client_processing_fee_amount [decimal(8,2)]
 * @property string $pqr_refund_cost [decimal(8,2)]
 * @property string $pqr_client_refund_cost [decimal(8,2)]
 */
class ProductQuoteRefund extends \yii\db\ActiveRecord implements Serializable
{
    use FieldsTrait;

    private const TYPE_RE_PROTECTION = 1;
    private const TYPE_VOLUNTARY_REFUND = 2;

    private const TYPE_LIST = [
        self::TYPE_RE_PROTECTION => 'Schedule Change',
        self::TYPE_VOLUNTARY_REFUND => 'Voluntary Refund'
    ];

    private const SHORT_TYPE_LIST = [
        self::TYPE_RE_PROTECTION => 'SC',
        self::TYPE_VOLUNTARY_REFUND => 'Vol'
    ];

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public static function create(
        $orderRefundId,
        $productQuoteId,
        $sellingPrice,
        $clientCurrency,
        $clientCurrencyRate,
        $caseId
    ): self {
        $refund = new self();
        $refund->pqr_order_refund_id = $orderRefundId;
        $refund->pqr_product_quote_id = $productQuoteId;
        $refund->pqr_selling_price = $sellingPrice;
        $refund->pqr_penalty_amount = 0;
        $refund->pqr_processing_fee_amount = 0;
        $refund->pqr_refund_amount = $refund->pqr_selling_price - $refund->pqr_penalty_amount - $refund->pqr_processing_fee_amount;
        $refund->pqr_client_currency = $clientCurrency;
        $refund->pqr_client_currency_rate = $clientCurrencyRate;
        $refund->pqr_client_selling_price = CurrencyHelper::roundUp($refund->pqr_selling_price * $refund->pqr_client_currency_rate);
        $refund->pqr_client_refund_amount = CurrencyHelper::roundUp($refund->pqr_refund_amount * $refund->pqr_client_currency_rate);
        $refund->pqr_case_id = $caseId;
        $refund->pqr_gid = self::generateGid();
        return $refund;
    }

    public static function createByScheduleChange(
        $orderRefundId,
        $productQuoteId,
        $sellingPrice,
        $clientCurrency,
        $clientCurrencyRate,
        $caseId
    ): self {
        $refund = self::create(
            $orderRefundId,
            $productQuoteId,
            $sellingPrice,
            $clientCurrency,
            $clientCurrencyRate,
            $caseId
        );
        $refund->pqr_status_id = ProductQuoteRefundStatus::PENDING;
        $refund->pqr_type_id = self::TYPE_RE_PROTECTION;
        $refund->detachBehavior('user');
        return $refund;
    }

    public static function createByVoluntaryRefund(
        $orderRefundId,
        $productQuoteId,
        $sellingPrice,
        $processingFee,
        $refundAmount,
        $penaltyAmount,
        $clientCurrency,
        $clientCurrencyRate,
        $clientSelling,
        $clientPenaltyAmount,
        $clientProcessingFeeAmount,
        $clientRefundAmount,
        $caseId,
        $cid,
        $data,
        $refundCost,
        $clientRefundCost
    ): self {
        $refund = self::create(
            $orderRefundId,
            $productQuoteId,
            $sellingPrice,
            $clientCurrency,
            $clientCurrencyRate,
            $caseId
        );
        $refund->pqr_type_id = self::TYPE_VOLUNTARY_REFUND;
        $refund->pqr_processing_fee_amount = $processingFee;
        $refund->pqr_refund_amount = $refundAmount;
        $refund->pqr_penalty_amount = $penaltyAmount;
        $refund->pqr_client_selling_price = $clientSelling;
        $refund->pqr_client_penalty_amount = $clientPenaltyAmount;
        $refund->pqr_client_processing_fee_amount = $clientProcessingFeeAmount;
        $refund->pqr_client_refund_amount = $clientRefundAmount;
        $refund->pqr_data_json = $data;
        $refund->pqr_cid = $cid;
        $refund->pqr_refund_cost = $refundCost;
        $refund->pqr_client_refund_cost = $clientRefundCost;
        $refund->detachBehavior('user');
        return $refund;
    }

    public function new(): void
    {
        $this->pqr_status_id = ProductQuoteRefundStatus::NEW;
    }

    public function error(): void
    {
        $this->pqr_status_id = ProductQuoteRefundStatus::ERROR;
    }

    public function declined(): void
    {
        $this->pqr_status_id = ProductQuoteRefundStatus::DECLINED;
    }

    public function inProgress(): void
    {
        $this->pqr_status_id = ProductQuoteRefundStatus::IN_PROGRESS;
    }

    public function inProcessing(): void
    {
        $this->pqr_status_id = ProductQuoteRefundStatus::PROCESSING;
    }

    public function processing(): void
    {
        $this->pqr_status_id = ProductQuoteRefundStatus::PROCESSING;
    }

    public function pending(): void
    {
        $this->pqr_status_id = ProductQuoteRefundStatus::PENDING;
    }

    public function isInProcessing(): bool
    {
        return $this->pqr_status_id === ProductQuoteRefundStatus::PROCESSING;
    }

    public function complete(): void
    {
        $this->pqr_status_id = ProductQuoteRefundStatus::COMPLETED;
    }

    public function isCompleted(): bool
    {
        return $this->pqr_status_id === ProductQuoteRefundStatus::COMPLETED;
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pqr_created_dt', 'pqr_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pqr_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'pqr_created_user_id',
                'updatedByAttribute' => 'pqr_updated_user_id',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_quote_refund';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pqr_order_refund_id', 'pqr_product_quote_id'], 'required'],
            [['pqr_order_refund_id', 'pqr_product_quote_id', 'pqr_status_id', 'pqr_created_user_id', 'pqr_updated_user_id'], 'integer'],
            [['pqr_selling_price', 'pqr_penalty_amount', 'pqr_processing_fee_amount', 'pqr_refund_amount',
                'pqr_client_currency_rate', 'pqr_client_selling_price', 'pqr_client_refund_amount',
                'pqr_client_penalty_amount', 'pqr_client_processing_fee_amount', 'pqr_refund_cost',
                'pqr_client_refund_cost'], 'number', 'min' => 0, 'max' => 999999.99],
            [['pqr_created_dt', 'pqr_updated_dt', 'pqr_expiration_dt'], 'safe'],
            [['pqr_client_currency'], 'string', 'max' => 3],
            [['pqr_client_currency'], 'default', 'value' => null],

            [['pqr_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['pqr_client_currency' => 'cur_code']],
            [['pqr_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqr_created_user_id' => 'id']],
            [['pqr_order_refund_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderRefund::class, 'targetAttribute' => ['pqr_order_refund_id' => 'orr_id']],
            [['pqr_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqr_updated_user_id' => 'id']],
            [['pqr_product_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['pqr_product_quote_id' => 'pq_id']],

            ['pqr_case_id', 'integer'],
            ['pqr_case_id', 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['pqr_case_id' => 'cs_id']],

            ['pqr_data_json', 'safe'],
            ['pqr_data_json', 'trim'],
            ['pqr_data_json', CheckJsonValidator::class],

            [['pqr_type_id'], 'integer'],
            [['pqr_type_id'], 'in', 'range' => array_keys(self::TYPE_LIST)],

            [['pqr_gid', 'pqr_cid'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pqr_id' => 'ID',
            'pqr_order_refund_id' => 'Order Refund ID',
            'pqr_product_quote_id' => 'Product Quote Id',
            'pqr_selling_price' => 'Selling Price',
            'pqr_penalty_amount' => 'Penalty Amount',
            'pqr_processing_fee_amount' => 'Processing Fee Amount',
            'pqr_refund_amount' => 'Refund Amount',
            'pqr_status_id' => 'Status ID',
            'pqr_client_currency' => 'Client Currency',
            'pqr_client_currency_rate' => 'Client Currency Rate',
            'pqr_client_selling_price' => 'Client Selling Price',
            'pqr_client_refund_amount' => 'Client Refund Amount',
            'pqr_created_user_id' => 'Created User ID',
            'pqr_updated_user_id' => 'Updated User ID',
            'pqr_created_dt' => 'Created Dt',
            'pqr_updated_dt' => 'Updated Dt',
            'pqr_case_id' => 'Case ID',
            'pqr_data_json' => 'Data',
            'pqr_type_id' => 'Type',
            'pqr_client_penalty_amount' => 'Client Penalty Amount',
            'pqr_client_processing_fee_amount' => 'Client Processing Fee',
            'pqr_refund_cost' => 'Refund Cost',
            'pqr_client_refund_cost' => 'Client Refund Cost',
        ];
    }

    /**
     * Gets query for [[PqrClientCurrency]].
     *
     * @return \yii\db\ActiveQuery|CurrencyQuery
     */
    public function getClientCurrency()
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'pqr_client_currency']);
    }

    /**
     * Gets query for [[PqrCreatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pqr_created_user_id']);
    }

    /**
     * Gets query for [[PqrOrderRefund]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderRefund()
    {
        return $this->hasOne(OrderRefund::class, ['orr_id' => 'pqr_order_refund_id']);
    }

    public function getCase()
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'pqr_case_id']);
    }

    /**
     * Gets query for [[PqrUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pqr_updated_user_id']);
    }

    /**
     * Gets query for [[PqrProductQuote]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductQuote()
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'pqr_product_quote_id']);
    }

    /**
     * Gets query for [[ProductQuoteObjectRefunds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductQuoteObjectRefunds()
    {
        return $this->hasMany(ProductQuoteObjectRefund::class, ['pqor_product_quote_refund_id' => 'pqr_id']);
    }
    /**
     * Gets query for [[ProductQuoteOptionRefunds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductQuoteOptionRefunds()
    {
        return $this->hasMany(ProductQuoteOptionRefund::class, ['pqor_product_quote_refund_id' => 'pqr_id']);
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find()
    {
        return new Scopes(get_called_class());
    }

    public function getApiDataMapped(): array
    {
        return [
            'id' => 'pqr_id',
            'gid' => 'pqr_gid',
            'cid' => 'pqr_cid',
            'productQuoteId' => 'pqr_product_quote_id',
            'productQuoteGid' => static function (ProductQuoteRefund $model) {
                return $model->productQuote->pq_gid ?? null;
            },
            'caseId' => 'pqr_case_id',
            'caseGid' => static function (ProductQuoteRefund $model) {
                return $model->case->cs_gid ?? null;
            },
            'orderId' => static function (ProductQuoteRefund $model) {
                return $model->productQuote->pq_order_id ?? null;
            },
            'orderGid' => static function (ProductQuoteRefund $model) {
                return $model->productQuote->pqOrder->or_gid ?? null;
            },
            'statusId' => 'pqr_status_id',
            'statusName' => static function (ProductQuoteRefund $model) {
                return ProductQuoteRefundStatus::getName($model->pqr_status_id);
            },
            'sellingPrice' => 'pqr_selling_price',
            'penaltyAmount' => 'pqr_penalty_amount',
            'processingFeeAmount' => 'pqr_processing_fee_amount',
            'refundAmount' => 'pqr_refund_amount',
            'clientCurrency' => 'pqr_client_currency',
            'clientCurrencyRate' => 'pqr_client_currency_rate',
            'clientSellingPrice' => 'pqr_client_selling_price',
            'clientRefundAmount' => 'pqr_client_refund_amount',
            'refundCost' => 'pqr_refund_cost',
            'clientRefundCost' => 'pqr_client_refund_cost',
            'createdDt' => 'pqr_created_dt',
            'updatedDt' => 'pqr_updated_dt',
        ];
    }

    /**
     * @return string[]
     */
    public static function getTypeList(): array
    {
        return self::TYPE_LIST;
    }

    /**
     * @return string[]
     */
    public static function getShortTypeList(): array
    {
        return self::SHORT_TYPE_LIST;
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return self::getTypeList()[$this->pqr_type_id] ?? '-';
    }

    /**
     * @return string
     */
    public function getShortTypeName(): string
    {
        return self::getShortTypeList()[$this->pqr_type_id] ?? '-';
    }

    public function cancel(): void
    {
        $this->pqr_status_id = ProductQuoteRefundStatus::CANCELED;
    }

    /**
     * @return string
     */
    public function getStatusLabel(): string
    {
        return $this->pqr_status_id ? ProductQuoteRefundStatus::asFormat($this->pqr_status_id) : '-';
    }

    public function getClientStatusName(): string
    {
        return $this->pqr_status_id ? ProductQuoteRefundStatus::getClientKeyStatusById($this->pqr_status_id) : '-';
    }

    public function getCountOfObjects(): int
    {
        return count($this->productQuoteObjectRefunds ?? []);
    }

    public function getCountOfOptions(): int
    {
        return count($this->productQuoteOptionRefunds ?? []);
    }

    public function getClientSellingPriceFormat(): string
    {
        return ($this->pqr_client_selling_price ? number_format($this->pqr_client_selling_price, 2) : '-')
            . ' ' . Html::encode($this->pqr_client_currency);
    }

    public function getClientRefundAmountPriceFormat(): string
    {
        return ($this->pqr_client_refund_amount ? number_format($this->pqr_client_refund_amount, 2) : '-')
            . ' ' . Html::encode($this->pqr_client_currency);
    }

    /**
     * @return string
     */
    public static function generateGid(): string
    {
        return md5(uniqid('pqr', true));
    }

    public function serialize(): array
    {
        return (new ProductQuoteRefundSerializer($this))->getData();
    }

    public function calculateSystemPrices(): void
    {
        $this->pqr_selling_price = CurrencyHelper::convertToBaseCurrency($this->pqr_client_selling_price, $this->clientCurrency->cur_base_rate);
        $this->pqr_refund_cost = CurrencyHelper::convertToBaseCurrency($this->pqr_client_refund_cost, $this->clientCurrency->cur_base_rate);
        $this->pqr_processing_fee_amount = CurrencyHelper::convertToBaseCurrency($this->pqr_client_processing_fee_amount, $this->clientCurrency->cur_base_rate);
        $this->pqr_penalty_amount = CurrencyHelper::convertToBaseCurrency($this->pqr_client_penalty_amount, $this->clientCurrency->cur_base_rate);
        $this->pqr_refund_amount = CurrencyHelper::convertToBaseCurrency($this->pqr_client_refund_amount, $this->clientCurrency->cur_base_rate);
    }

    public static function typeVoluntary(): int
    {
        return self::TYPE_VOLUNTARY_REFUND;
    }

    public static function typeReProtection(): int
    {
        return self::TYPE_RE_PROTECTION;
    }
}
