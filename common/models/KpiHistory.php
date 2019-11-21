<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

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
 * @property string $kh_base_amount
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
            [['kh_base_amount', 'kh_profit_bonus', 'kh_manual_bonus', 'kh_estimation_profit'], 'number'],
            [['kh_description'], 'string'],
            [['kh_super_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['kh_super_id' => 'id']],
            [['kh_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['kh_user_id' => 'id']],
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
            'kh_created_dt' => 'Created',
            'kh_updated_dt' => 'Updated',
            'kh_agent_approved_dt' => 'Agent Approved',
            'kh_super_approved_dt' => 'Super Approved',
            'kh_super_id' => 'Super ID',
            'kh_base_amount' => 'Base $',
            'kh_bonus_active' => 'Bonus Active',
            'kh_commission_percent' => 'Commission %',
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
        return $this->hasOne(Employee::class, ['id' => 'kh_super_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKhUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'kh_user_id']);
    }

    public function getSalary()
    {
        $salary = 0;
        $profit = $this->kh_estimation_profit * $this->kh_commission_percent / 100;
        $salary = $profit + $this->kh_base_amount + $this->kh_profit_bonus + $this->kh_manual_bonus;

        return number_format($salary, 2);
    }


    /**
     * @return KpiHistory
     */
    public static function recalculateSalary(Employee $agent, \DateTime $start, \DateTime $end)
    {
        $salary = $agent->calculateSalaryBetween($start, $end);
        $salaryParams = $agent->paramsForSalary();

        $khDate = $end->format('Y-m-d');
        $khUserId = $agent->id;

        $kpiHistory = KpiHistory::find()->where(['kh_date_dt' => $khDate, 'kh_user_id' => $khUserId])->one();
        if(!$kpiHistory){
            $kpiHistory = new KpiHistory();
            $kpiHistory->kh_date_dt = $khDate;
            $kpiHistory->kh_user_id = $khUserId;
        }
        if(empty($kpiHistory->kh_agent_approved_dt) && empty($kpiHistory->kh_super_approved_dt)){
            $kpiHistory->kh_base_amount = $salaryParams['base_amount'];
            $kpiHistory->kh_commission_percent = $salaryParams['commission_percent'];
            $kpiHistory->kh_bonus_active = $salaryParams['bonus_active'];
            $kpiHistory->kh_profit_bonus = $salary['bonus'];
            $kpiHistory->kh_estimation_profit = $salary['startProfit'];
        }

        if(empty($kpiHistory->kh_agent_approved_dt)|| empty($kpiHistory->kh_super_approved_dt)){
            $kpiHistory->kh_agent_approved_dt = null;
            $kpiHistory->kh_super_approved_dt = null;
        }

        return $kpiHistory;
    }
}
