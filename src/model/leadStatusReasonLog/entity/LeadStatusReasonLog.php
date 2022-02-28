<?php

namespace src\model\leadStatusReasonLog\entity;

use common\models\LeadFlow;
use common\models\query\LeadFlowQuery;
use src\model\leadStatusReason\entity\LeadStatusReason;
use src\model\leadStatusReason\entity\Scopes;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_status_reason_log".
 *
 * @property int $lsrl_id
 * @property int|null $lsrl_lead_flow_id
 * @property int|null $lsrl_lead_status_reason_id
 * @property string|null $lsrl_comment
 * @property string|null $lsrl_created_dt
 *
 * @property LeadFlow $lsrlLeadFlow
 * @property LeadStatusReason $lsrlLeadStatusReason
 */
class LeadStatusReasonLog extends \yii\db\ActiveRecord
{
    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lsrl_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_status_reason_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lsrl_lead_flow_id', 'lsrl_lead_status_reason_id'], 'integer'],
            [['lsrl_created_dt'], 'safe'],
            [['lsrl_comment'], 'string', 'max' => 255],
            [['lsrl_lead_flow_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadFlow::class, 'targetAttribute' => ['lsrl_lead_flow_id' => 'id']],
            [['lsrl_lead_status_reason_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadStatusReason::class, 'targetAttribute' => ['lsrl_lead_status_reason_id' => 'lsr_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lsrl_id' => 'Lsrl ID',
            'lsrl_lead_flow_id' => 'Lsrl Lead Flow ID',
            'lsrl_lead_status_reason_id' => 'Lsrl Lead Status Reason ID',
            'lsrl_comment' => 'Lsrl Comment',
            'lsrl_created_dt' => 'Lsrl Created Dt',
        ];
    }

    /**
     * Gets query for [[LsrlLeadFlow]].
     *
     * @return \yii\db\ActiveQuery|LeadFlowQuery
     */
    public function getLsrlLeadFlow()
    {
        return $this->hasOne(LeadFlow::class, ['id' => 'lsrl_lead_flow_id']);
    }

    /**
     * Gets query for [[LsrlLeadStatusReason]].
     *
     * @return \yii\db\ActiveQuery|Scopes
     */
    public function getLsrlLeadStatusReason()
    {
        return $this->hasOne(LeadStatusReason::class, ['lsr_id' => 'lsrl_lead_status_reason_id']);
    }

    /**
     * {@inheritdoc}
     * @return LeadStatusReasonLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LeadStatusReasonLogQuery(get_called_class());
    }
}
