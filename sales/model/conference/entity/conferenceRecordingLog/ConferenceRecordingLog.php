<?php

namespace sales\model\conference\entity\conferenceRecordingLog;

use common\models\Conference;
use common\models\Employee;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "conference_recording_log".
 *
 * @property int $cfrl_id
 * @property string|null $cfrl_conference_sid
 * @property int $cfrl_user_id
 * @property string|null $cfrl_created_dt
 * @property int $cfrl_year
 * @property int $cfrl_month
 *
 * @property Employee $user
 * @property Conference $conference
 */
class ConferenceRecordingLog extends \yii\db\ActiveRecord
{
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cfrl_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ]
        ];
    }

    public function rules(): array
    {
        return [
            [['cfrl_id', 'cfrl_year', 'cfrl_month'], 'unique', 'targetAttribute' => ['cfrl_id', 'cfrl_year', 'cfrl_month']],

            ['cfrl_conference_sid', 'string', 'max' => 34],

            ['cfrl_created_dt', 'safe'],

            ['cfrl_month', 'required'],
            ['cfrl_month', 'integer'],

            ['cfrl_user_id', 'required'],
            ['cfrl_user_id', 'integer'],

            ['cfrl_conference_sid', 'exist', 'skipOnError' => true, 'targetClass' => Conference::class, 'targetAttribute' => ['cfrl_conference_sid' => 'cf_sid']],
            ['cfrl_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cfrl_user_id' => 'id']],

            ['cfrl_year', 'required'],
            ['cfrl_year', 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'cfrl_id' => 'ID',
            'cfrl_conference_sid' => 'Conference Sid',
            'cfrl_user_id' => 'User ID',
            'cfrl_created_dt' => 'Created Dt',
            'cfrl_year' => 'Year',
            'cfrl_month' => 'Month',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'conference_recording_log';
    }

    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cfrl_user_id']);
    }

    public function getConference(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Conference::class, ['cf_sid' => 'cfrl_conference_sid']);
    }

    public static function create(string $conferenceSid, int $userId, int $year, int $month): self
    {
        $self = new self();
        $self->cfrl_conference_sid = $conferenceSid;
        $self->cfrl_user_id = $userId;
        $self->cfrl_year = $year;
        $self->cfrl_month = $month;
        return $self;
    }
}
