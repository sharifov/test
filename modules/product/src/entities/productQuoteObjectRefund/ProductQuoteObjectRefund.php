<?php

namespace modules\product\src\entities\productQuoteObjectRefund;

use common\models\Currency;
use common\models\Employee;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product_quote_object_refund".
 *
 * @property int $pqor_id
 * @property int $pqor_product_quote_refund_id
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
 * @property Currency $clientCurrency
 * @property Employee $createdUser
 * @property ProductQuoteRefund $productQuoteRefund
 * @property Employee $updatedUser
 */
class ProductQuoteObjectRefund extends \yii\db\ActiveRecord
{
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
            [['pqor_product_quote_refund_id'], 'required'],
            [['pqor_product_quote_refund_id', 'pqor_status_id', 'pqor_created_user_id', 'pqor_updated_user_id'], 'integer'],
            [['pqor_selling_price', 'pqor_penalty_amount', 'pqor_processing_fee_amount', 'pqor_refund_amount', 'pqor_client_currency_rate', 'pqor_client_selling_price', 'pqor_client_refund_amount'], 'number', 'min' => 0, 'max' => 999999.99],
            [['pqor_created_dt', 'pqor_updated_dt'], 'safe'],
            [['pqor_client_currency'], 'string', 'max' => 3],
            [['pqor_client_currency'], 'default', 'value' => null],
            [['pqor_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['pqor_client_currency' => 'cur_code']],
            [['pqor_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqor_created_user_id' => 'id']],
            [['pqor_product_quote_refund_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuoteRefund::class, 'targetAttribute' => ['pqor_product_quote_refund_id' => 'pqr_id']],
            [['pqor_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqor_updated_user_id' => 'id']],
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
}
