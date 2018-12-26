<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "kpi_history".
 *
 * @property int $kh_id
 * @property int $kh_user_id
 * @property string $kh_date_dt
 * @property string $kh_created_dt
 * @property string $kh_updated_dt
 * @property string $kh_agent_approved_dt
 * @property string $kh_super_approved_dt
 * @property int $kh_super_id
 * @property string $kh_bonus_amount
 * @property int $kh_bonus_active
 * @property int $kh_commission_percent
 * @property string $kh_profit_bonus
 * @property string $kh_manual_bonus
 * @property string $kh_estimation_profit
 * @property string $kh_description
 *
 * @property Employee $khSuper
 * @property Employee $khUser
 */
class KpiHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kpi_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kh_user_id'], 'required'],
            [['kh_user_id', 'kh_super_id', 'kh_bonus_active', 'kh_commission_percent'], 'integer'],
            [['kh_date_dt', 'kh_created_dt', 'kh_updated_dt', 'kh_agent_approved_dt', 'kh_super_approved_dt'], 'safe'],
            [['kh_bonus_amount', 'kh_profit_bonus', 'kh_manual_bonus', 'kh_estimation_profit'], 'number'],
            [['kh_description'], 'string'],
            [['kh_super_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['kh_super_id' => 'id']],
            [['kh_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['kh_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kh_id' => 'ID',
            'kh_user_id' => 'User ID',
            'kh_date_dt' => 'Date',
            'kh_created_dt' => 'Created Dt',
            'kh_updated_dt' => 'Updated Dt',
            'kh_agent_approved_dt' => 'Agent Approved Dt',
            'kh_super_approved_dt' => 'Super Approved Dt',
            'kh_super_id' => 'Super ID',
            'kh_bonus_amount' => 'Bonus Amount',
            'kh_bonus_active' => 'Bonus Active',
            'kh_commission_percent' => 'Commission Percent',
            'kh_profit_bonus' => 'Profit Bonus',
            'kh_manual_bonus' => 'Manual Bonus',
            'kh_estimation_profit' => 'Estimation Profit',
            'kh_description' => 'Description',
        ];
    }


    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['kh_created_dt', 'kh_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['kh_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKhSuper()
    {
        return $this->hasOne(Employee::className(), ['id' => 'kh_super_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKhUser()
    {
        return $this->hasOne(Employee::className(), ['id' => 'kh_user_id']);
    }
}
