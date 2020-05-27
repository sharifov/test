<?php

namespace sales\model\userVoiceMail\entity;

use common\models\Employee;
use common\models\Language;
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
	private const MAX_COUNT_ROWS = 10;

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
            ['uvm_say_language', 'required'],
			['uvm_say_language', 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['uvm_say_language' => 'language_id']],

            ['uvm_say_text_message', 'string'],

            ['uvm_say_voice', 'string', 'max' => 255],

            ['uvm_transcribe_enable', 'integer'],

            ['uvm_updated_dt', 'safe'],

            ['uvm_updated_user_id', 'integer'],
            ['uvm_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['uvm_updated_user_id' => 'id']],

            ['uvm_user_id', 'integer'],
            ['uvm_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['uvm_user_id' => 'id']],

            ['uvm_voice_file_message', 'string', 'max' => 255],

			['uvm_user_id', 'checkCountOfRows']
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
            'uvm_id' => 'ID',
            'uvm_user_id' => 'User ID',
            'uvm_name' => 'Name',
            'uvm_say_text_message' => 'Say Text Message',
            'uvm_say_language' => 'Say Language',
            'uvm_say_voice' => 'Say Voice',
            'uvm_voice_file_message' => 'Voice File Message',
            'uvm_record_enable' => 'Record Enable',
            'uvm_max_recording_time' => 'Max Recording Time',
            'uvm_transcribe_enable' => 'Transcribe Enable',
            'uvm_enabled' => 'Enabled',
            'uvm_created_dt' => 'Created Dt',
            'uvm_updated_dt' => 'Updated Dt',
            'uvm_created_user_id' => 'Created User ID',
            'uvm_updated_user_id' => 'Updated User ID',
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

    public function checkCountOfRows($attribute, $params, $validator): void
	{
		if ($this->getIsNewRecord()) {
			$count = self::find()->where(['uvm_user_id' => $this->uvm_user_id])->count();

			$maxRows = Yii::$app->params['user_voice_mail'] ?? self::MAX_COUNT_ROWS;
			if ($count+1 > $maxRows) {
				$this->addError('uvm_user_id', 'Maximum number of entries exceeded: ' . $maxRows);
			}
		}
	}
}
