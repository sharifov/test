<?php

namespace sales\model\conference\entity\conferenceParticipantStats;

use common\models\Conference;
use common\models\Employee;

/**
 * This is the model class for table "{{%conference_participant_stats}}".
 *
 * @property int $cps_id
 * @property int|null $cps_cf_id
 * @property string|null $cps_cf_sid
 * @property string|null $cps_participant_identity
 * @property int|null $cps_user_id
 * @property string $cps_created_dt
 * @property int|null $cps_duration
 * @property int|null $cps_talk_time
 * @property int|null $cps_hold_time
 *
 * @property Conference $conference
 * @property Employee $user
 */
class ConferenceParticipantStats extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['cps_cf_id', 'integer'],
            ['cps_cf_id', 'exist', 'skipOnError' => true, 'targetClass' => Conference::class, 'targetAttribute' => ['cps_cf_id' => 'cf_id']],

            ['cps_cf_sid', 'required'],
            ['cps_cf_sid', 'string', 'max' => 34],
            ['cps_cf_sid', 'trim'],

            ['cps_created_dt', 'required'],
            ['cps_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['cps_duration', 'integer', 'max' => 32500],

            ['cps_hold_time', 'integer', 'max' => 32500],

            ['cps_talk_time', 'integer', 'max' => 32500],

            ['cps_participant_identity', 'string', 'max' => 50],
            ['cps_participant_identity', 'trim'],

            ['cps_user_id', 'integer'],
            ['cps_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cps_user_id' => 'id']],
        ];
    }

    public function getConference(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Conference::class, ['cf_id' => 'cps_cf_id']);
    }

    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cps_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cps_id' => 'ID',
            'cps_cf_id' => 'Conference ID',
            'cps_cf_sid' => 'Conference Sid',
            'cps_participant_identity' => 'Participant Identity',
            'cps_user_id' => 'User ID',
            'cps_created_dt' => 'Created Dt',
            'cps_duration' => 'Duration',
            'cps_talk_time' => 'Talk Time',
            'cps_hold_time' => 'Hold Time',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%conference_participant_stats}}';
    }
}
