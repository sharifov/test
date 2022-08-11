<?php

namespace modules\product\src\entities\productQuoteOptionRefund;

use common\components\validators\CheckJsonValidator;
use common\models\Currency;
use common\models\Employee;
use frontend\helpers\JsonHelper;
use modules\order\src\entities\orderRefund\OrderRefund;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productQuoteOptionRefund\serializer\ProductQuoteOptionRefundSerializer;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use src\behaviors\StringToJsonBehavior;
use src\entities\serializer\Serializable;
use src\traits\FieldsTrait;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product_quote_option_refund".
 *
 * @property int $pqor_id
 * @property int $pqor_product_quote_refund_id
 * @property int|null $pqor_product_quote_option_id
 * @property float|null $pqor_selling_price
 * @property float|null $pqor_penalty_amount
 * @property float|null $pqor_processing_fee_amount
 * @property float|null $pqor_refund_amount
 * @property int|null $pqor_status_id
 * @property string|null $pqor_client_currency
 * @property float|null $pqor_client_currency_rate
 * @property float|null $pqor_client_selling_price
 * @property float|null $pqor_client_refund_amount
 * @property int|null $pqor_created_user_id
 * @property int|null $pqor_updated_user_id
 * @property string|null $pqor_created_dt
 * @property string|null $pqor_updated_dt
 *
 * @property ProductQuoteOption $productQuoteOption
 * @property ProductQuoteRefund $productQuoteRefund
 * @property Employee $createdUser
 * @property Employee $updatedUser
 * @property Currency $clientCurrency
 * @property int $pqor_order_refund_id [int]
 * @property bool $pqor_refund_allow [tinyint(1)]
 * @property string $pqor_data_json [json]
 */
class ProductQuoteOptionRefund extends \yii\db\ActiveRecord implements Serializable
{
    use FieldsTrait;

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pqor_created_dt', 'pqor_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pqor_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'pqor_created_user_id',
                'updatedByAttribute' => 'pqor_updated_user_id',
            ],
            'stringToJson' => [
                'class' => StringToJsonBehavior::class,
                'jsonColumn' => 'pqor_data_json',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'product_quote_option_refund';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['pqor_product_quote_refund_id'], 'required'],
            [['pqor_product_quote_refund_id', 'pqor_product_quote_option_id', 'pqor_status_id', 'pqor_created_user_id', 'pqor_updated_user_id', 'pqor_order_refund_id'], 'integer'],
            [['pqor_selling_price', 'pqor_penalty_amount', 'pqor_processing_fee_amount', 'pqor_refund_amount', 'pqor_client_currency_rate', 'pqor_client_selling_price', 'pqor_client_refund_amount'], 'number', 'min' => 0, 'max' => 999999.99],
            [['pqor_created_dt', 'pqor_updated_dt'], 'safe'],
            [['pqor_client_currency'], 'string', 'max' => 3],
            [['pqor_client_currency'], 'default', 'value' => null],
            [['pqor_refund_allow'], 'boolean'],
            [['pqor_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqor_created_user_id' => 'id']],
            [['pqor_product_quote_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuoteOption::class, 'targetAttribute' => ['pqor_product_quote_option_id' => 'pqo_id']],
            [['pqor_product_quote_refund_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuoteRefund::class, 'targetAttribute' => ['pqor_product_quote_refund_id' => 'pqr_id']],
            [['pqor_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqor_updated_user_id' => 'id']],
            [['pqor_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['pqor_client_currency' => 'cur_code']],
            [['pqor_order_refund_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderRefund::class, 'targetAttribute' => ['pqor_order_refund_id' => 'orr_id']],
            ['pqor_data_json', 'safe'],
            ['pqor_data_json', 'trim'],
            ['pqor_data_json', CheckJsonValidator::class],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'pqor_id' => 'ID',
            'pqor_product_quote_refund_id' => 'Product Quote Refund ID',
            'pqor_product_quote_option_id' => 'Product Quote Option ID',
            'pqor_selling_price' => 'Selling Price',
            'pqor_penalty_amount' => 'Penalty Amount',
            'pqor_processing_fee_amount' => 'Processing Fee Amount',
            'pqor_refund_amount' => 'Refund Amount',
            'pqor_status_id' => 'Status ID',
            'pqor_client_currency' => 'Client Currency',
            'pqor_client_currency_rate' => 'Client Currency Rate',
            'pqor_client_selling_price' => 'Client Selling Price',
            'pqor_client_refund_amount' => 'Client Refund Amount',
            'pqor_created_user_id' => 'Created User ID',
            'pqor_updated_user_id' => 'Updated User ID',
            'pqor_created_dt' => 'Created Dt',
            'pqor_updated_dt' => 'Updated Dt',
            'pqor_data_json' => 'Data',
        ];
    }

    /**
     * Gets query for [[PqorClientCurrency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClientCurrency()
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'pqor_client_currency']);
    }

    /**
     * Gets query for [[PqorCreatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pqor_created_user_id']);
    }

    /**
     * Gets query for [[PqorProductQuoteOption]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductQuoteOption()
    {
        return $this->hasOne(ProductQuoteOption::class, ['pqo_id' => 'pqor_product_quote_option_id']);
    }

    /**
     * Gets query for [[PqorProductQuoteRefund]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductQuoteRefund()
    {
        return $this->hasOne(ProductQuoteRefund::class, ['pqr_id' => 'pqor_product_quote_refund_id']);
    }

    /**
     * Gets query for [[PqorUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pqor_updated_user_id']);
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
            "type" => static function (self $model) {
                return JsonHelper::decode($model->pqor_data_json)['type'] ?? '';
            },
            "amount" => static function (self $model) {
                return (float)$model->pqor_client_selling_price;
            },
            'amountPerPax' => static function (self $model) {
                return JsonHelper::decode($model->pqor_data_json)['amountPerPax'] ?? [];
            },
            "refundable" => static function (self $model) {
                return (float)$model->pqor_client_refund_amount;
            },
            "details" => static function (self $model) {
                return JsonHelper::decode($model->pqor_data_json)['details'] ?? [];
            },
            "status" => static function (self $model) {
                return JsonHelper::decode($model->pqor_data_json)['status'] ?? '';
            },
            "refundAllow" => static function (self $model) {
                return (bool)$model->pqor_refund_allow;
            }
        ];
    }

    public static function create(
        int $orderRefundId,
        int $productQuoteRefundId,
        ?int $productQuoteOptionId,
        ?float $sellingPrice,
        ?float $penaltyAmount,
        ?float $processingFeeAmount,
        ?float $refundAmount,
        string $clientCurrency,
        float $clientCurrencyRate,
        ?float $clientSellingPrice,
        ?float $clientRefundAmount,
        bool $refundAllow,
        ?array $data
    ): self {
        $self = new self();
        $self->pqor_order_refund_id = $orderRefundId;
        $self->pqor_product_quote_refund_id = $productQuoteRefundId;
        $self->pqor_product_quote_option_id = $productQuoteOptionId;
        $self->pqor_selling_price = $sellingPrice;
        $self->pqor_penalty_amount = $penaltyAmount;
        $self->pqor_processing_fee_amount = $processingFeeAmount;
        $self->pqor_refund_amount = $refundAmount;
        $self->pqor_client_currency = $clientCurrency;
        $self->pqor_client_currency_rate = $clientCurrencyRate;
        $self->pqor_client_selling_price = $clientSellingPrice;
        $self->pqor_client_refund_amount = $clientRefundAmount;
        $self->pqor_refund_allow = $refundAllow;
        $self->pqor_data_json = $data;
        return $self;
    }

    public function pending(): void
    {
        $this->pqor_status_id = ProductQuoteOptionRefundStatus::PENDING;
    }

    public function new(): void
    {
        $this->pqor_status_id = ProductQuoteOptionRefundStatus::NEW;
    }

    /**
     * Sets quote option in status "expired"
     *
     * @return void
     */
    public function expired(): void
    {
        $this->pqor_status_id = ProductQuoteOptionRefundStatus::EXPIRED;
    }

    public function serialize(): array
    {
        return (new ProductQuoteOptionRefundSerializer($this))->getData();
    }
}
