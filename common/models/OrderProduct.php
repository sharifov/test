<?php

namespace common\models;

use common\models\query\OrderProductQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "order_product".
 *
 * @property int $orp_order_id
 * @property int $orp_product_quote_id
 * @property int|null $orp_created_user_id
 * @property string|null $orp_created_dt
 *
 * @property Employee $orpCreatedUser
 * @property Order $orpOrder
 * @property ProductQuote $orpProductQuote
 */
class OrderProduct extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['orp_order_id', 'orp_product_quote_id'], 'required'],
            [['orp_order_id', 'orp_product_quote_id', 'orp_created_user_id'], 'integer'],
            [['orp_created_dt'], 'safe'],
            [['orp_order_id', 'orp_product_quote_id'], 'unique', 'targetAttribute' => ['orp_order_id', 'orp_product_quote_id']],
            [['orp_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['orp_created_user_id' => 'id']],
            [['orp_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['orp_order_id' => 'or_id']],
            [['orp_product_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['orp_product_quote_id' => 'pq_id']],
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['orp_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'orp_created_user_id',
                'updatedByAttribute' => 'orp_created_user_id',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'orp_order_id' => 'Order ID',
            'orp_product_quote_id' => 'Product Quote ID',
            'orp_created_user_id' => 'Created User ID',
            'orp_created_dt' => 'Created Dt',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrpCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'orp_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrpOrder()
    {
        return $this->hasOne(Order::class, ['or_id' => 'orp_order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrpProductQuote()
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'orp_product_quote_id']);
    }

    /**
     * {@inheritdoc}
     * @return OrderProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderProductQuery(get_called_class());
    }
}
