<?php

namespace modules\product\src\entities\productQuoteOptionRefund;

use common\models\Currency;
use common\models\Employee;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use sales\traits\FieldsTrait;
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
 */
class ProductQuoteOptionRefund extends \yii\db\ActiveRecord
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
            [['pqor_product_quote_refund_id', 'pqor_product_quote_option_id', 'pqor_status_id', 'pqor_created_user_id', 'pqor_updated_user_id'], 'integer'],
            [['pqor_selling_price', 'pqor_penalty_amount', 'pqor_processing_fee_amount', 'pqor_refund_amount', 'pqor_client_currency_rate', 'pqor_client_selling_price', 'pqor_client_refund_amount'], 'number', 'min' => 0, 'max' => 999999.99],
            [['pqor_created_dt', 'pqor_updated_dt'], 'safe'],
            [['pqor_client_currency'], 'string', 'max' => 3],
            [['pqor_client_currency'], 'default', 'value' => null],
            [['pqor_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqor_created_user_id' => 'id']],
            [['pqor_product_quote_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuoteOption::class, 'targetAttribute' => ['pqor_product_quote_option_id' => 'pqo_id']],
            [['pqor_product_quote_refund_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuoteRefund::class, 'targetAttribute' => ['pqor_product_quote_refund_id' => 'pqr_id']],
            [['pqor_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqor_updated_user_id' => 'id']],
            [['pqor_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['pqor_client_currency' => 'cur_code']],
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
                return null;
            },
            "airlinePenalty" => 'pqor_penalty_amount',
            "refundAmount" => 'pqor_client_refund_amount',
            "status" => static function (self $model) {
                return ProductQuoteOptionRefundStatus::getName($model->pqor_status_id);
            }
        ];
    }
}
