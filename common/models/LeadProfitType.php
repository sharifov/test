<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lead_profit_type".
 *
 * @property int $lpt_profit_type_id
 * @property int|null $lpt_diff_rule
 * @property int|null $lpt_commission_min
 * @property int|null $lpt_commission_max
 * @property int|null $lpt_commission_fix
 * @property int|null $lpt_created_user_id
 * @property int|null $lpt_updated_user_id
 * @property string|null $lpt_created_dt
 * @property string|null $lpt_updated_dt
 *
 * @property Employee $createdUser
 * @property Employee $updatedUser
 */
class LeadProfitType extends \yii\db\ActiveRecord
{
	public const LEAD_PROFIT_TYPE_NEW = 1;
	public const LEAD_PROFIT_TYPE_FUP = 2;
	public const LEAD_PROFIT_TYPE_REFERRAL = 3;
	public const LEAD_PROFIT_TYPE_RETURN = 4;
	public const LEAD_PROFIT_TYPE_GROUP = 5;

	public const LEAD_PROFIT_TYPE_LIST = [
		self::LEAD_PROFIT_TYPE_NEW => 'New',
		self::LEAD_PROFIT_TYPE_FUP => 'F-UP',
		self::LEAD_PROFIT_TYPE_REFERRAL => 'Referral',
		self::LEAD_PROFIT_TYPE_RETURN => 'Return',
		self::LEAD_PROFIT_TYPE_GROUP => 'Group'
	];

	public const MAX_PERCENT_VALUE = 100;
	public const MIN_PERCENT_VALUE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_profit_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lpt_profit_type_id'], 'required'],
            [['lpt_profit_type_id', 'lpt_diff_rule', 'lpt_commission_min', 'lpt_commission_max', 'lpt_commission_fix', 'lpt_created_user_id', 'lpt_updated_user_id'], 'integer'],
            [['lpt_created_dt', 'lpt_updated_dt'], 'safe'],
            [['lpt_profit_type_id'], 'unique'],
			['lpt_profit_type_id', 'in', 'range' => array_keys(self::getProfitTypeList())],
			[['lpt_diff_rule', 'lpt_commission_min', 'lpt_commission_max', 'lpt_commission_fix'], 'default', 'value' => 0],
			[['lpt_diff_rule', 'lpt_commission_min', 'lpt_commission_max', 'lpt_commission_fix'], 'number', 'min' => self::MIN_PERCENT_VALUE, 'max' => self::MAX_PERCENT_VALUE]
        ];
    }

	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['lpt_created_dt', 'lpt_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['lpt_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
			],
			'user' => [
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'lpt_created_user_id',
				'updatedByAttribute' => 'lpt_updated_user_id',
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lpt_profit_type_id' => 'Profit Type ID',
            'lpt_diff_rule' => 'Difference from rule %',
            'lpt_commission_min' => 'Commission Min %',
            'lpt_commission_max' => 'Commission Max %',
            'lpt_commission_fix' => 'Commission Fix %',
            'lpt_created_user_id' => 'Created User ID',
            'lpt_updated_user_id' => 'Updated User ID',
            'lpt_created_dt' => 'Created Dt',
            'lpt_updated_dt' => 'Updated Dt',
        ];
    }

    public static function getProfitTypeList(): array
	{
		return self::LEAD_PROFIT_TYPE_LIST;
	}

	public static function getProfitTypeName(int $id): ?string
	{
		return self::getProfitTypeList()[$id] ?? null;
	}

	public function getCreatedUser(): ActiveQuery
	{
		return $this->hasOne(Employee::class, ['id' => 'lpt_created_user_id']);
	}

	public function getUpdatedUser(): ActiveQuery
	{
		return $this->hasOne(Employee::class, ['id' => 'lpt_updated_user_id']);
	}
}
