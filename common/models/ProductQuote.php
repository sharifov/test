<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product_quote".
 *
 * @property int $pq_id
 * @property string $pq_gid
 * @property string $pr_name
 * @property int $pq_product_id
 * @property int $pq_order_id
 * @property string $pq_description
 * @property int $pq_status_id
 * @property string $pq_price
 * @property string $pq_origin_price
 * @property string $pq_client_price
 * @property string $pq_service_fee_sum
 * @property string $pq_origin_currency
 * @property string $pq_client_currency
 * @property string $pq_origin_currency_rate
 * @property string $pq_client_currency_rate
 * @property int $pq_owner_user_id
 * @property int $pq_created_user_id
 * @property int $pq_updated_user_id
 * @property string $pq_created_dt
 * @property string $pq_updated_dt
 *
 * @property OfferProduct[] $offerProducts
 * @property Offer[] $opOffers
 * @property Currency $pqClientCurrency
 * @property Employee $pqCreatedUser
 * @property Order $pqOrder
 * @property Currency $pqOriginCurrency
 * @property Employee $pqOwnerUser
 * @property Product $pqProduct
 * @property Employee $pqUpdatedUser
 */
class ProductQuote extends \yii\db\ActiveRecord
{
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
            [['pr_name'], 'string', 'max' => 40],
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
            'pr_name' => 'Name',
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
                'createdByAttribute' => 'pq_created_user_id',
                'updatedByAttribute' => 'pq_updated_user_id',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferProducts()
    {
        return $this->hasMany(OfferProduct::class, ['op_product_quote_id' => 'pq_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOpOffers()
    {
        return $this->hasMany(Offer::class, ['of_id' => 'op_offer_id'])->viaTable('offer_product', ['op_product_quote_id' => 'pq_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPqClientCurrency()
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'pq_client_currency']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPqCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pq_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPqOrder()
    {
        return $this->hasOne(Order::class, ['or_id' => 'pq_order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPqOriginCurrency()
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'pq_origin_currency']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPqOwnerUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pq_owner_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPqProduct()
    {
        return $this->hasOne(Product::class, ['pr_id' => 'pq_product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPqUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pq_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return ProductQuoteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductQuoteQuery(get_called_class());
    }
}
