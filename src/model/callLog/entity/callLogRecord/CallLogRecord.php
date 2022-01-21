<?php

namespace src\model\callLog\entity\callLogRecord;

use src\model\callLog\entity\callLog\CallLog;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%call_log_record}}".
 *
 * @property int $clr_cl_id
 * @property string $clr_record_sid
 * @property int|null $clr_duration
 *
 * @property CallLog $log
 */
class CallLogRecord extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%call_log_record}}';
    }

    public function rules(): array
    {
        return [
            ['clr_cl_id', 'required'],
            ['clr_cl_id', 'integer'],
            ['clr_cl_id', 'unique'],

            ['clr_duration', 'integer', 'max' => 32500],

            ['clr_record_sid', 'required'],
            ['clr_record_sid', 'string', 'max' => 34],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'clr_cl_id' => 'Log',
            'clr_record_sid' => 'Record Sid',
            'clr_duration' => 'Duration',
        ];
    }

    public function getLog(): ActiveQuery
    {
        return $this->hasOne(CallLog::class, ['cl_id' => 'clr_cl_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
