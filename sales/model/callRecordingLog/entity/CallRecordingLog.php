<?php

namespace sales\model\callRecordingLog\entity;

use common\models\Employee;
use sales\model\callLog\entity\callLog\CallLog;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "call_recording_log".
 *
 * @property int $crl_id
 * @property string|null $crl_call_sid
 * @property int $crl_user_id
 * @property string|null $crl_created_dt
 * @property int $crl_year
 * @property int $crl_month
 *
 * @property Employee $user
 * @property CallLog $callLog
 */
class CallRecordingLog extends \yii\db\ActiveRecord
{
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['crl_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ]
        ];
    }

    public function rules(): array
    {
        return [
            [['crl_id', 'crl_year', 'crl_month'], 'unique', 'targetAttribute' => ['crl_id', 'crl_year', 'crl_month']],

            ['crl_call_sid', 'string', 'max' => 34],

            ['crl_created_dt', 'safe'],

            ['crl_user_id', 'required'],
            ['crl_user_id', 'integer'],
            ['crl_call_sid', 'exist', 'skipOnError' => true, 'targetClass' => CallLog::class, 'targetAttribute' => ['crl_call_sid' => 'cl_call_sid']],
            ['crl_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['crl_user_id' => 'id']],

            ['crl_year', 'required'],
            ['crl_year', 'integer'],

            ['crl_month', 'required'],
            ['crl_month', 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'crl_id' => 'ID',
            'crl_call_sid' => 'Call Sid',
            'crl_user_id' => 'User ID',
            'crl_created_dt' => 'Created Dt',
            'crl_year' => 'Year',
            'crl_month' => 'Month',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'crl_user_id']);
    }

    public function getCallLog(): \yii\db\ActiveQuery
    {
        return $this->hasOne(CallLog::class, ['cl_call_sid' => 'crl_call_sid']);
    }

    public static function tableName(): string
    {
        return 'call_recording_log';
    }

    public static function create(string $callSid, int $userId, int $year, int $month): CallRecordingLog
    {
        $self = new self();
        $self->crl_call_sid = $callSid;
        $self->crl_user_id = $userId;
        $self->crl_year = $year;
        $self->crl_month = $month;
        return $self;
    }
}
