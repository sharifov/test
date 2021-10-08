<?php

namespace modules\product\src\entities\productQuoteRefund;

use common\models\Currency;
use common\models\Employee;
use common\models\query\CurrencyQuery;
use modules\order\src\entities\orderRefund\OrderRefund;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefund;
use sales\entities\cases\Cases;
use sales\services\CurrencyHelper;
use sales\traits\FieldsTrait;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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
 * @property int|null $pqr_case_id
 *
 * @property Currency $clientCurrency
 * @property Employee $createdUser
 * @property OrderRefund $orderRefund
 * @property Employee $updatedUser
 * @property ProductQuote $productQuote
 * @property ProductQuoteObjectRefund[] $productQuoteObjectRefunds
 * @property ProductQuoteOptionRefund[] $productQuoteOptionRefunds
 * @property Cases $case
 */
class ProductQuoteRefund extends \yii\db\ActiveRecord
{
    use FieldsTrait;

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
        $refund->detachBehavior('user');
        return $refund;
    }

    public function error(): void
    {
        $this->pqr_status_id = ProductQuoteRefundStatus::ERROR;
    }

    public function processing(): void
    {
        $this->pqr_status_id = ProductQuoteRefundStatus::PROCESSING;
    }

    public function isInProcessing(): bool
    {
        return $this->pqr_status_id === ProductQuoteRefundStatus::PROCESSING;
    }

    public function done(): void
    {
        $this->pqr_status_id = ProductQuoteRefundStatus::DONE;
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
            [['pqr_selling_price', 'pqr_penalty_amount', 'pqr_processing_fee_amount', 'pqr_refund_amount', 'pqr_client_currency_rate', 'pqr_client_selling_price', 'pqr_client_refund_amount'], 'number', 'min' => 0, 'max' => 999999.99],
            [['pqr_created_dt', 'pqr_updated_dt'], 'safe'],
            [['pqr_client_currency'], 'string', 'max' => 3],
            [['pqr_client_currency'], 'default', 'value' => null],

            [['pqr_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['pqr_client_currency' => 'cur_code']],
            [['pqr_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqr_created_user_id' => 'id']],
            [['pqr_order_refund_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderRefund::class, 'targetAttribute' => ['pqr_order_refund_id' => 'orr_id']],
            [['pqr_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqr_updated_user_id' => 'id']],
            [['pqr_product_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['pqr_product_quote_id' => 'pq_id']],

            ['pqr_case_id', 'integer'],
            ['pqr_case_id', 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['pqr_case_id' => 'cs_id']],
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
                return $model->productQuote->pq_gid ?? null;
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
            'createdDt' => 'pqr_created_dt',
            'updatedDt' => 'pqr_updated_dt',
        ];
    }
}
