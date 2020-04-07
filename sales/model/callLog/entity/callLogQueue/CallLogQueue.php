<?php

namespace sales\model\callLog\entity\callLogQueue;

use sales\model\callLog\entity\callLog\CallLog;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%call_log_queue}}".
 *
 * @property int $clq_cl_id
 * @property int|null $clq_queue_time
 * @property int|null $clq_access_count
 * @property boolean|null $clq_is_transfer
 *
 * @property CallLog $log
 */
class CallLogQueue extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%call_log_queue}}';
    }

    public function rules(): array
    {
        return [
            ['clq_cl_id', 'required'],
            ['clq_cl_id', 'integer'],
            ['clq_cl_id', 'unique'],
            ['clq_cl_id', 'exist', 'skipOnError' => true, 'targetClass' => CallLog::class, 'targetAttribute' => ['clq_cl_id' => 'cl_id']],

            ['clq_queue_time', 'integer', 'min' => 0, 'max' => 32767],

            ['clq_access_count', 'integer', 'min' => 0, 'max' => 127],

            ['clq_is_transfer', 'boolean'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'clq_cl_id' => 'Log',
            'clq_queue_time' => 'Queue Time',
            'clq_access_count' => 'Access Count',
            'clq_is_transfer' => 'Is Transfer',
        ];
    }

    public function getLog(): ActiveQuery
    {
        return $this->hasOne(CallLog::class, ['cl_id' => 'clq_cl_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
