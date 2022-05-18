<?php

namespace modules\product\src\entities\productQuoteObjectRefund;

use common\components\validators\CheckJsonValidator;
use common\models\Currency;
use common\models\Employee;
use frontend\helpers\JsonHelper;
use modules\flight\src\entities\flightQuoteTicketRefund\FlightQuoteTicketRefund;
use modules\product\src\entities\productQuoteObjectRefund\serializer\ProductQuoteObjectRefundSerializer;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\interfaces\ProductQuoteObjectRefundStructure;
use src\entities\serializer\Serializable;
use src\repositories\NotFoundException;
use src\services\CurrencyHelper;
use src\traits\FieldsTrait;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product_quote_object_refund".
 *
 * @property int $pqor_id
 * @property int $pqor_product_quote_refund_id
 * @property int $pqor_quote_object_id [int]
 * @property float|null $pqor_selling_price
 * @property float|null $pqor_penalty_amount
 * @property float|null $pqor_processing_fee_amount
 * @property float|null $pqor_refund_amount
 * @property int|null $pqor_status_id
 * @property string|null $pqor_client_currency
 * @property float|null $pqor_client_currency_rate
 * @property float|null $pqor_client_selling_price
 * @property float|null $pqor_client_refund_amount
 * @property string|null $pqor_title [varchar(50)]
 * @property int|null $pqor_created_user_id
 * @property int|null $pqor_updated_user_id
 * @property string|null $pqor_created_dt
 * @property string|null $pqor_updated_dt
 *
 * @property Currency $clientCurrency
 * @property Employee $createdUser
 * @property ProductQuoteRefund $productQuoteRefund
 * @property Employee $updatedUser
 * @property string $pqor_data_json [json]
 * @property string $pqor_client_penalty_amount [decimal(8,2)]
 * @property string $pqor_client_processing_fee_amount [decimal(8,2)]
 */
class ProductQuoteObjectRefund extends \yii\db\ActiveRecord implements Serializable
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_quote_object_refund';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pqor_product_quote_refund_id', 'pqor_quote_object_id'], 'required'],
            [['pqor_product_quote_refund_id', 'pqor_status_id', 'pqor_created_user_id', 'pqor_updated_user_id', 'pqor_quote_object_id'], 'integer'],
            [['pqor_selling_price', 'pqor_penalty_amount', 'pqor_processing_fee_amount', 'pqor_refund_amount', 'pqor_client_currency_rate', 'pqor_client_selling_price', 'pqor_client_refund_amount', 'pqor_client_penalty_amount', 'pqor_client_processing_fee_amount'], 'number', 'min' => 0, 'max' => 999999.99],
            [['pqor_created_dt', 'pqor_updated_dt'], 'safe'],
            [['pqor_client_currency'], 'string', 'max' => 3],
            [['pqor_title'], 'string', 'max' => 50],
            [['pqor_client_currency'], 'default', 'value' => null],
            [['pqor_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['pqor_client_currency' => 'cur_code']],
            [['pqor_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqor_created_user_id' => 'id']],
            [['pqor_product_quote_refund_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuoteRefund::class, 'targetAttribute' => ['pqor_product_quote_refund_id' => 'pqr_id']],
            [['pqor_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqor_updated_user_id' => 'id']],
            [['pqor_data_json'], 'safe'],
            [['pqor_data_json'], CheckJsonValidator::class],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pqor_id' => 'ID',
            'pqor_product_quote_refund_id' => 'Product Quote Refund ID',
            'pqor_quote_object_id' => 'Product Quote Object ID',
            'pqor_selling_price' => 'Selling Price',
            'pqor_penalty_amount' => 'Penalty Amount',
            'pqor_processing_fee_amount' => 'Processing Fee Amount',
            'pqor_refund_amount' => 'Refund Amount',
            'pqor_status_id' => 'Status ID',
            'pqor_client_currency' => 'Client Currency',
            'pqor_client_currency_rate' => 'Client Currency Rate',
            'pqor_client_selling_price' => 'Client Selling Price',
            'pqor_client_refund_amount' => 'Client Refund Amount',
            'pqor_title' => 'Title',
            'pqor_created_user_id' => 'Created User ID',
            'pqor_updated_user_id' => 'Updated User ID',
            'pqor_created_dt' => 'Created Dt',
            'pqor_updated_dt' => 'Updated Dt',
            'pqor_data_json' => 'Data',
            'pqor_client_penalty_amount' => 'Client Penalty Amount',
            'pqor_client_processing_fee_amount' => 'Client Processing Fee'
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
            "number" => static function (self $model) {
                return FlightQuoteTicketRefund::findOne(['fqtr_id' => $model->pqor_quote_object_id])->fqtr_ticket_number ?? '';
            },
            "airlinePenalty" => static function (self $model) {
                return (float)$model->pqor_penalty_amount;
            },
            "processingFee" => static function (self $model) {
                return (float)$model->pqor_processing_fee_amount;
            },
            "refundable" => static function (self $model) {
                return (float)$model->pqor_client_refund_amount;
            },
            "selling" => static function (self $model) {
                return (float)$model->pqor_client_selling_price;
            },
            "currency" => 'pqor_client_currency',
            "status" => static function (self $model) {
                return JsonHelper::decode($model->pqor_data_json)['status'] ?? '';
            },
            'refundAllowed' => static function (self $model) {
                return (bool)(JsonHelper::decode($model->pqor_data_json)['refundAllowed'] ?? null);
            }
        ];
    }

    public static function create(
        int $productQuoteRefundId,
        ?int $productQuoteObjectId,
        float $sellingPrice,
        float $penaltyAmount,
        float $processingFeeAmount,
        float $refundAmount,
        string $clientCurrency,
        float $clientCurrencyRate,
        float $clientSellingPrice,
        float $clientPenaltyAmount,
        float $clientProcessingFeeAmount,
        float $clientRefundAmount,
        ?string $title,
        array $dataJson
    ): self {
        $self = new self();
        $self->pqor_product_quote_refund_id = $productQuoteRefundId;
        $self->pqor_quote_object_id = $productQuoteObjectId;
        $self->pqor_selling_price = $sellingPrice;
        $self->pqor_penalty_amount = $penaltyAmount;
        $self->pqor_processing_fee_amount = $processingFeeAmount;
        $self->pqor_refund_amount = $refundAmount;
        $self->pqor_client_currency = $clientCurrency;
        $self->pqor_client_currency_rate = $clientCurrencyRate;
        $self->pqor_client_selling_price = $clientSellingPrice;
        $self->pqor_client_penalty_amount = $clientPenaltyAmount;
        $self->pqor_client_processing_fee_amount = $clientProcessingFeeAmount;
        $self->pqor_client_refund_amount = $clientRefundAmount;
        $self->pqor_title = $title;
        $self->pqor_data_json = $dataJson;
        return $self;
    }

    public function pending(): void
    {
        $this->pqor_status_id = ProductQuoteObjectRefundStatus::PENDING;
    }

    public function new(): void
    {
        $this->pqor_status_id = ProductQuoteObjectRefundStatus::NEW;
    }

    /**
     * Sets quote in status "expired"
     *
     * @return void
     */
    public function expired(): void
    {
        $this->pqor_status_id = ProductQuoteObjectRefundStatus::EXPIRED;
    }

    public function serialize(): array
    {
        return (new ProductQuoteObjectRefundSerializer($this))->getData();
    }

    public function calculateSystemPrices(): void
    {
        $this->pqor_penalty_amount = CurrencyHelper::convertToBaseCurrency($this->pqor_client_penalty_amount, $this->clientCurrency->cur_base_rate);
        $this->pqor_processing_fee_amount = CurrencyHelper::convertToBaseCurrency($this->pqor_client_processing_fee_amount, $this->clientCurrency->cur_base_rate);
        $this->pqor_refund_amount = CurrencyHelper::convertToBaseCurrency($this->pqor_client_refund_amount, $this->clientCurrency->cur_base_rate);
        $this->pqor_selling_price = CurrencyHelper::convertToBaseCurrency($this->pqor_client_selling_price, $this->clientCurrency->cur_base_rate);
    }
}
