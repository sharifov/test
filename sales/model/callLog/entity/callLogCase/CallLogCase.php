<?php

namespace sales\model\callLog\entity\callLogCase;

use sales\entities\cases\Cases;
use sales\entities\cases\CaseStatusLog;
use sales\model\callLog\entity\callLog\CallLog;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%call_log_case}}".
 *
 * @property int $clc_cl_id
 * @property int $clc_case_id
 * @property int|null $clc_case_status_log_id
 *
 * @property CallLog $log
 * @property Cases $case
 * @property CaseStatusLog $statusLog
 */
class CallLogCase extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%call_log_case}}';
    }

    public function rules(): array
    {
        return [
            [['clc_cl_id', 'clc_case_id'], 'required'],
            [['clc_cl_id', 'clc_case_id'], 'integer'],
            [['clc_cl_id', 'clc_case_id'], 'unique', 'targetAttribute' => ['clc_cl_id', 'clc_case_id']],

            ['clc_cl_id', 'exist', 'skipOnError' => true, 'targetClass' => CallLog::class, 'targetAttribute' => ['clc_cl_id' => 'cl_id']],
            ['clc_case_id', 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['clc_case_id' => 'cs_id']],

            ['clc_case_status_log_id', 'integer'],
            ['clc_case_status_log_id', 'exist', 'skipOnError' => true, 'targetClass' => CaseStatusLog::class, 'targetAttribute' => ['clc_case_status_log_id' => 'csl_id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'clc_cl_id' => 'Log',
            'clc_case_id' => 'Case',
            'clc_case_status_log_id' => 'Status Log Id',
        ];
    }

    public function getLog(): ActiveQuery
    {
        return $this->hasOne(CallLog::class, ['cl_id' => 'clc_cl_id']);
    }

    public function getCase(): ActiveQuery
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'clc_case_id']);
    }

    public function getStatusLog(): ActiveQuery
    {
        return $this->hasOne(CaseStatusLog::class, ['csl_id' => 'clc_case_status_log_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
