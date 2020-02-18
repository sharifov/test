<?php

namespace modules\order\src\entities\orderProduct;

use common\models\Employee;
use modules\order\src\entities\order\events\OrderRecalculateProfitAmountEvent;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\entities\EventTrait;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
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
    use EventTrait;

    public static function tableName(): string
    {
        return 'order_product';
    }

    public function rules(): array
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

    public function attributeLabels(): array
    {
        return [
            'orp_order_id' => 'Order ID',
            'orpOrder' => 'Order',
            'orp_product_quote_id' => 'Product Quote ID',
            'orpProductQuote' => 'Product Quote',
            'orp_created_user_id' => 'Created User',
            'orpCreatedUser' => 'Created User',
            'orp_created_dt' => 'Created Dt',
        ];
    }

    /**
     * @param int $orderId
     * @param int $productQuoteId
     * @return static
     */
    public static function create(int $orderId, int $productQuoteId): self
    {
        $model = new static();
        $model->orp_order_id = $orderId;
        $model->orp_product_quote_id = $productQuoteId;
        $model->recordEvent(new OrderRecalculateProfitAmountEvent([$model->orpOrder]));
        return $model;
    }

    /**
     * @return ActiveQuery
     */
    public function getOrpCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'orp_created_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrpOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['or_id' => 'orp_order_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrpProductQuote(): ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'orp_product_quote_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
