<?php

namespace common\models;

use common\models\query\UserBonusRulesQuery;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_bonus_rules".
 *
 * @property int $ubr_exp_month
 * @property int $ubr_kpi_percent
 * @property int $ubr_order_profit
 * @property float|null $ubr_value
 * @property int|null $ubr_created_user_id
 * @property int|null $ubr_updated_user_id
 * @property string|null $ubr_created_dt
 * @property string|null $ubr_updated_dt
 *
 * @property Employee $ubrCreatedUser
 * @property Employee $ubrUpdatedUser
 */
class UserBonusRules extends \yii\db\ActiveRecord
{
	public const VALUE_MAX = 100;
	public const VALUE_MIN = 0;

	public const EXP_MAX_VALUE = 32767;
	public const EXP_MIN_VALUE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_bonus_rules';
    }

	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['ubr_created_dt', 'ubr_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['ubr_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
			],
			'user' => [
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'ubr_created_user_id',
				'updatedByAttribute' => 'ubr_updated_user_id',
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ubr_exp_month', 'ubr_kpi_percent', 'ubr_order_profit'], 'required'],
            [['ubr_exp_month', 'ubr_kpi_percent', 'ubr_order_profit', 'ubr_created_user_id', 'ubr_updated_user_id'], 'integer'],
			[['ubr_exp_month'], 'number', 'min' => self::EXP_MIN_VALUE, 'max' => self::EXP_MAX_VALUE],
			[['ubr_order_profit', 'ubr_value'], 'number', 'min' => self::VALUE_MIN],
			[['ubr_kpi_percent'], 'number', 'max' => self::VALUE_MAX, 'min' => self::VALUE_MIN],
			[['ubr_value'], 'filter', 'filter' => 'floatval'],
            [['ubr_created_dt', 'ubr_updated_dt'], 'safe'],
            [['ubr_exp_month', 'ubr_kpi_percent', 'ubr_order_profit'], 'unique', 'targetAttribute' => ['ubr_exp_month', 'ubr_kpi_percent', 'ubr_order_profit']],
            [['ubr_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ubr_created_user_id' => 'id']],
            [['ubr_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ubr_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ubr_exp_month' => 'Exp Month',
            'ubr_kpi_percent' => 'Kpi Percent',
            'ubr_order_profit' => 'Order Profit',
            'ubr_value' => 'Value',
            'ubr_created_user_id' => 'Created User ID',
            'ubr_updated_user_id' => 'Updated User ID',
            'ubr_created_dt' => 'Created Dt',
            'ubr_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * Gets query for [[UbrCreatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUbrCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ubr_created_user_id']);
    }

    /**
     * Gets query for [[UbrUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUbrUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ubr_updated_user_id']);
    }

	/**
	 * @return UserBonusRulesQuery|\yii\db\ActiveQuery
	 */
	public static function find()
	{
		return new UserBonusRulesQuery(static::class);
	}
}
