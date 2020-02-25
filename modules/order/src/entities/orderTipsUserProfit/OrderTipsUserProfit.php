<?php

namespace modules\order\src\entities\orderTipsUserProfit;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use modules\order\src\entities\order\Order;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "order_tips_user_profit".
 *
 * @property int $otup_order_id
 * @property int $otup_user_id
 * @property int $otup_percent
 * @property float|null $otup_amount
 * @property string|null $otup_created_dt
 * @property string|null $otup_updated_dt
 * @property int|null $otup_created_user_id
 * @property int|null $otup_updated_user_id
 *
 * @property Employee $otupCreatedUser
 * @property Order $otupOrder
 * @property Employee $otupUpdatedUser
 * @property Employee $otupUser
 */
class OrderTipsUserProfit extends \yii\db\ActiveRecord
{
	public const MAX_PERCENT = 100;
	public const MIN_PERCENT = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_tips_user_profit';
    }

	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['otup_created_dt', 'otup_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['otup_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
			],
			'user' => [
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'otup_created_user_id',
				'updatedByAttribute' => 'otup_updated_user_id',
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['otup_order_id', 'otup_user_id', 'otup_percent'], 'required'],
            [['otup_order_id', 'otup_user_id', 'otup_percent', 'otup_created_user_id', 'otup_updated_user_id'], 'integer'],
			['otup_percent', 'integer', 'max' => self::MAX_PERCENT , 'min' => self::MIN_PERCENT],
			[['otup_amount'], 'number'],
            [['otup_created_dt', 'otup_updated_dt'], 'safe'],
            [['otup_order_id', 'otup_user_id'], 'unique', 'targetAttribute' => ['otup_order_id', 'otup_user_id']],
            [['otup_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['otup_created_user_id' => 'id']],
            [['otup_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['otup_order_id' => 'or_id']],
            [['otup_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['otup_updated_user_id' => 'id']],
            [['otup_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['otup_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'otup_order_id' => 'Order',
            'otup_user_id' => 'User',
            'otup_percent' => 'Percent',
            'otup_amount' => 'Amount',
            'otup_created_dt' => 'Created',
            'otup_updated_dt' => 'Updated',
            'otup_created_user_id' => 'Created User',
            'otup_updated_user_id' => 'Updated User',
        ];
    }

    /**
     * Gets query for [[OtupCreatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getOtupCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'otup_created_user_id']);
    }

    /**
     * Gets query for [[OtupOrder]].
     *
     * @return \yii\db\ActiveQuery|\modules\order\src\entities\order\Scopes
     */
    public function getOtupOrder()
    {
        return $this->hasOne(Order::class, ['or_id' => 'otup_order_id']);
    }

    /**
     * Gets query for [[OtupUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getOtupUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'otup_updated_user_id']);
    }

    /**
     * Gets query for [[OtupUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getOtupUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'otup_user_id']);
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
