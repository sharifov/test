<?php

namespace common\models;

use common\models\query\UserParamsQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_params".
 *
 * @property int $up_user_id
 * @property int $up_commission_percent
 * @property string $up_base_amount
 * @property string $up_updated_dt
 * @property int $up_updated_user_id
 * @property string $up_timezone
 * @property string $up_work_start_tm
 * @property int $up_work_minutes
 * @property bool $up_bonus_active
 * @property int $up_inbox_show_limit_leads
 * @property int $up_business_inbox_show_limit_leads
 * @property int $up_default_take_limit_leads
 * @property int $up_min_percent_for_take_leads
 * @property int $up_frequency_minutes
 * @property int $up_call_expert_limit
 * @property bool $up_leaderboard_enabled
 *
 * @property Employee $upUpdatedUser
 * @property Employee $upUser
 * @property bool $up_call_user_level [tinyint]
 */
class UserParams extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_params';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['up_user_id', 'up_work_minutes', 'up_work_start_tm', 'up_timezone'], 'required'],
            [['up_user_id', 'up_commission_percent', 'up_updated_user_id', 'up_bonus_active', 'up_work_minutes', 'up_inbox_show_limit_leads', 'up_business_inbox_show_limit_leads', 'up_default_take_limit_leads', 'up_min_percent_for_take_leads', 'up_frequency_minutes', 'up_call_expert_limit', 'up_leaderboard_enabled'], 'integer'],
            [['up_base_amount'], 'number'],
            [['up_updated_dt'], 'safe'],
            [['up_timezone'], 'string', 'max' => 40],
            [['up_user_id'], 'unique'],
            [['up_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['up_updated_user_id' => 'id']],
            [['up_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['up_user_id' => 'id']],

            ['up_call_user_level', 'default', 'value' => 0],
            ['up_call_user_level', 'integer', 'min' => -128, 'max' => 127],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'up_user_id' => 'User ID',
            'up_commission_percent' => 'Commission Percent',
            'up_base_amount' => 'Base Amount',
            'up_updated_dt' => 'Updated Dt',
            'up_updated_user_id' => 'Updated User ID',
            'up_bonus_active' => 'Bonus Is Active',
            'up_work_start_tm' => 'Work Start Time',
            'up_work_minutes' => 'Work Minutes',
            'up_timezone' => 'Timezone',
            'up_inbox_show_limit_leads' => 'Inbox show limit leads',
            'up_business_inbox_show_limit_leads' => 'Business Inbox show limit leads',
            'up_default_take_limit_leads' => 'Default take limit leads',
            'up_min_percent_for_take_leads' => 'Min percent for take leads',
            'up_frequency_minutes' => 'Take Frequency Minutes',
            'up_call_expert_limit' => 'Call Expert limit',
            'up_leaderboard_enabled' => 'Leader Board Enabled',
            'up_call_user_level' => 'Call Priority Level'
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['up_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['up_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'up_updated_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'up_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return UserParamsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserParamsQuery(static::class);
    }

    public static function getActiveTimezones(): array
    {
        return self::find()->select('up_timezone')->indexBy('up_timezone')->column();
    }
}
