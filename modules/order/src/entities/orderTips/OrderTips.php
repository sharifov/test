<?php

namespace modules\order\src\entities\orderTips;

use modules\order\src\entities\order\Order;
use sales\entities\EventTrait;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "order_tips".
 *
 * @property int $ot_id
 * @property int|null $ot_order_id
 * @property float|null $ot_client_amount
 * @property float|null $ot_amount
 * @property int|null $ot_user_profit_percent
 * @property float|null $ot_user_profit
 * @property string|null $ot_description
 * @property string|null $ot_created_dt
 * @property string|null $ot_updated_dt
 *
 * @property Order $otOrder
 */
class OrderTips extends \yii\db\ActiveRecord
{
	use EventTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'order_tips';
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
					ActiveRecord::EVENT_BEFORE_INSERT => ['ot_created_dt', 'ot_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['ot_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
			]
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ot_order_id'], 'required'],
            [['ot_order_id', 'ot_user_profit_percent'], 'integer'],
            [['ot_user_profit_percent'], 'number', 'max' => 100, 'min' => 0],
            [['ot_client_amount', 'ot_amount', 'ot_user_profit'], 'number'],
            [['ot_created_dt', 'ot_updated_dt'], 'safe'],
            [['ot_description'], 'string', 'max' => 500],
            [['ot_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['ot_order_id' => 'or_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ot_id' => 'ID',
            'ot_order_id' => 'Order ID',
            'ot_client_amount' => 'Client Amount',
            'ot_amount' => 'Amount',
            'ot_user_profit' => 'User Profit',
            'ot_user_profit_percent' => 'User Profit Percent',
            'ot_description' => 'Description',
            'ot_created_dt' => 'Created Dt',
            'ot_updated_dt' => 'Updated Dt',
            'otOrder' => 'Order',
        ];
    }

    /**
     * Gets query for [[OtOrder]].
     *
     * @return \yii\db\ActiveQuery|\modules\order\src\entities\order\Scopes
     */
    public function getOtOrder()
    {
        return $this->hasOne(Order::class, ['or_id' => 'ot_order_id']);
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
