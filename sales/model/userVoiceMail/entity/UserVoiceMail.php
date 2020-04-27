<?php

namespace sales\model\userVoiceMail\entity;

use common\models\Employee;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_voice_mail".
 *
 * @property int $uvm_id
 * @property int|null $uvm_user_id
 * @property string|null $uvm_name
 * @property string|null $uvm_say_text_message
 * @property string|null $uvm_say_language
 * @property string|null $uvm_say_voice
 * @property string|null $uvm_voice_file_message
 * @property int|null $uvm_record_enable
 * @property int|null $uvm_max_recording_time
 * @property int|null $uvm_transcribe_enable
 * @property int|null $uvm_enabled
 * @property string|null $uvm_created_dt
 * @property string|null $uvm_updated_dt
 * @property int|null $uvm_created_user_id
 * @property int|null $uvm_updated_user_id
 *
 * @property Employee $uvmCreatedUser
 * @property Employee $uvmUpdatedUser
 * @property Employee $uvmUser
 */
class UserVoiceMail extends \yii\db\ActiveRecord
{
	/**
	 * @return array
	 */
	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['uvm_created_dt', 'uvm_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['uvm_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s')
			],
			'user' => [
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'uvm_created_user_id',
				'updatedByAttribute' => 'uvm_updated_user_id',
			],
		];
	}

    public function rules(): array
    {
        return [
            ['uvm_created_dt', 'safe'],

            ['uvm_created_user_id', 'integer'],
            ['uvm_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['uvm_created_user_id' => 'id']],

            ['uvm_enabled', 'integer'],

            ['uvm_max_recording_time', 'integer'],

            ['uvm_name', 'string', 'max' => 50],

            ['uvm_record_enable', 'integer'],

            ['uvm_say_language', 'string', 'max' => 10],

            ['uvm_say_text_message', 'string'],

            ['uvm_say_voice', 'string', 'max' => 255],

            ['uvm_transcribe_enable', 'integer'],

            ['uvm_updated_dt', 'safe'],

            ['uvm_updated_user_id', 'integer'],
            ['uvm_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['uvm_updated_user_id' => 'id']],

            ['uvm_user_id', 'integer'],
            ['uvm_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['uvm_user_id' => 'id']],

            ['uvm_voice_file_message', 'string', 'max' => 255],
        ];
    }

    public function getUvmCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'uvm_created_user_id']);
    }

    public function getUvmUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'uvm_updated_user_id']);
    }

    public function getUvmUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'uvm_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'uvm_id' => 'Uvm ID',
            'uvm_user_id' => 'Uvm User ID',
            'uvm_name' => 'Uvm Name',
            'uvm_say_text_message' => 'Uvm Say Text Message',
            'uvm_say_language' => 'Uvm Say Language',
            'uvm_say_voice' => 'Uvm Say Voice',
            'uvm_voice_file_message' => 'Uvm Voice File Message',
            'uvm_record_enable' => 'Uvm Record Enable',
            'uvm_max_recording_time' => 'Uvm Max Recording Time',
            'uvm_transcribe_enable' => 'Uvm Transcribe Enable',
            'uvm_enabled' => 'Uvm Enabled',
            'uvm_created_dt' => 'Uvm Created Dt',
            'uvm_updated_dt' => 'Uvm Updated Dt',
            'uvm_created_user_id' => 'Uvm Created User ID',
            'uvm_updated_user_id' => 'Uvm Updated User ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'user_voice_mail';
    }
}
