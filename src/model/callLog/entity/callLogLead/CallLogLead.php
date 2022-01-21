<?php

namespace src\model\callLog\entity\callLogLead;

use common\models\Lead;
use common\models\LeadFlow;
use src\model\callLog\entity\callLog\CallLog;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%call_log_lead}}".
 *
 * @property int $cll_cl_id
 * @property int $cll_lead_id
 * @property int|null $cll_lead_flow_id
 *
 * @property CallLog $log
 * @property Lead $lead
 * @property LeadFlow $leadFlow
 */
class CallLogLead extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%call_log_lead}}';
    }

    public function rules(): array
    {
        return [
            [['cll_cl_id', 'cll_lead_id'], 'required'],
            [['cll_cl_id', 'cll_lead_id'], 'integer'],
            [['cll_cl_id', 'cll_lead_id'], 'unique', 'targetAttribute' => ['cll_cl_id', 'cll_lead_id']],

            ['cll_cl_id', 'exist', 'skipOnError' => true, 'targetClass' => CallLog::class, 'targetAttribute' => ['cll_cl_id' => 'cl_id']],
            ['cll_lead_flow_id', 'integer'],
            ['cll_lead_flow_id', 'exist', 'skipOnError' => true, 'targetClass' => LeadFlow::class, 'targetAttribute' => ['cll_lead_flow_id' => 'id']],
            ['cll_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['cll_lead_id' => 'id']],

        ];
    }

    public function attributeLabels(): array
    {
        return [
            'cll_cl_id' => 'Log',
            'cll_lead_id' => 'Lead',
            'cll_lead_flow_id' => 'Lead Flow Id',
        ];
    }

    public function getLog(): ActiveQuery
    {
        return $this->hasOne(CallLog::class, ['cl_id' => 'cll_cl_id']);
    }

    public function getLead(): ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'cll_lead_id']);
    }

    public function getLeadFlow(): ActiveQuery
    {
        return $this->hasOne(LeadFlow::class, ['id' => 'cll_lead_flow_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
