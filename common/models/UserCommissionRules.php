<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_commission_rules".
 *
 * @property int $ucr_exp_month
 * @property int $ucr_kpi_percent
 * @property int $ucr_order_profit
 * @property float|null $ucr_value
 * @property int|null $ucr_created_user_id
 * @property int|null $ucr_updated_user_id
 * @property string|null $ucr_created_dt
 * @property string|null $ucr_updated_dt
 *
 * @property Employee $ucrCreatedUser
 * @property Employee $ucrUpdatedUser
 */
class UserCommissionRules extends \yii\db\ActiveRecord
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
        return 'user_commission_rules';
    }

	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['ucr_created_dt', 'ucr_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['ucr_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
			],
			'user' => [
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'ucr_created_user_id',
				'updatedByAttribute' => 'ucr_updated_user_id',
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ucr_exp_month', 'ucr_kpi_percent', 'ucr_order_profit'], 'required'],
            [['ucr_exp_month', 'ucr_kpi_percent', 'ucr_order_profit', 'ucr_created_user_id', 'ucr_updated_user_id'], 'integer'],
            [['ucr_value'], 'number', 'min' => self::VALUE_MIN, 'max' => self::VALUE_MAX],
            [['ucr_exp_month'], 'number', 'min' => self::EXP_MIN_VALUE, 'max' => self::EXP_MAX_VALUE],
            [['ucr_order_profit'], 'number', 'min' => self::VALUE_MIN],
			[['ucr_kpi_percent'], 'number', 'max' => self::VALUE_MAX, 'min' => self::VALUE_MIN],
			[['ucr_value'], 'filter', 'filter' => 'floatval'],
			[['ucr_created_dt', 'ucr_updated_dt'], 'safe'],
            [['ucr_exp_month', 'ucr_kpi_percent', 'ucr_order_profit'], 'unique', 'targetAttribute' => ['ucr_exp_month', 'ucr_kpi_percent', 'ucr_order_profit']],
            [['ucr_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ucr_created_user_id' => 'id']],
            [['ucr_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ucr_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ucr_exp_month' => 'Experience Month',
            'ucr_kpi_percent' => 'Kpi Percent',
            'ucr_order_profit' => 'Order Profit',
            'ucr_value' => 'Value',
            'ucr_created_user_id' => 'Created User ID',
            'ucr_updated_user_id' => 'Updated User ID',
            'ucr_created_dt' => 'Created Dt',
            'ucr_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * Gets query for [[UcrCreatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUcrCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ucr_created_user_id']);
    }

    /**
     * Gets query for [[UcrUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUcrUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ucr_updated_user_id']);
    }
}
