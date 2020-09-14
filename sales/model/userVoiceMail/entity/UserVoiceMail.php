<?php

namespace sales\model\userVoiceMail\entity;

use common\models\Employee;
use common\models\Language;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

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

	private const VOICE_ALICE = 'alice';
	private const VOICE_MAN = 'man';
	private const VOICE_WOMAN = 'woman';

	private const ALLOWED_SAY_VOICE = [
		self::VOICE_ALICE => self::VOICE_ALICE,
		self::VOICE_MAN => self::VOICE_MAN,
		self::VOICE_WOMAN => self::VOICE_WOMAN,
	];

	private const ALLOWED_VOICE_LANGUAGES = [
		self::VOICE_ALICE => [
			'da-DK',
			'da-DE',
			'en-AU',
			'en-CA',
			'en-GB',
			'en-IN',
			'en-US',
			'ca-ES',
			'es-ES',
			'es-MX',
			'fi-FI',
			'fi-CA',
			'fr-CA',
			'it-IT',
			'ja-JP',
			'ko-KR',
			'nb-NO',
			'nl-NL',
			'pl-PL',
			'pt-PT',
			'ru-RU',
			'sv-SE',
			'zn-CN',
			'zh-HK',
			'zh-TW',
		],
		self::VOICE_MAN => [
			'en-GB',
			'en-PI',
			'en-UD',
			'en-US',
			'es-ES',
			'es-LA',
			'fr-CA',
			'fr-FR',
			'de-DE',
		],
		self::VOICE_WOMAN => [
			'en-GB',
			'en-PI',
			'en-UD',
			'en-US',
			'es-ES',
			'es-LA',
			'fr-CA',
			'fr-FR',
			'de-DE',
		],
	];

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

            ['uvm_enabled', 'boolean'],

            ['uvm_max_recording_time', 'integer', 'max' => 3600],

            ['uvm_name', 'required'],
            ['uvm_name', 'string', 'max' => 50],

            ['uvm_record_enable', 'integer'],

            ['uvm_say_language', 'string', 'max' => 10],
            ['uvm_say_language', 'default', 'value' => null],
			['uvm_say_language', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true, 'targetClass' => Language::class, 'targetAttribute' => ['uvm_say_language' => 'language_id']],

            ['uvm_say_text_message', 'string'],

            ['uvm_say_voice', 'string', 'max' => 255],

            ['uvm_transcribe_enable', 'integer'],

            ['uvm_updated_dt', 'safe'],

            ['uvm_updated_user_id', 'integer'],
            ['uvm_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['uvm_updated_user_id' => 'id']],

            ['uvm_user_id', 'integer'],
            ['uvm_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['uvm_user_id' => 'id']],

            ['uvm_voice_file_message', 'string', 'max' => 255],

			['uvm_user_id', 'checkCountOfRows'],
			[['uvm_say_voice', 'uvm_say_language'], 'checkAllowedLanguages']
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

	public function getAllowedLanguagesByVoice(string $voice): array
	{
		return self::ALLOWED_VOICE_LANGUAGES[$voice] ?? [];
	}

	public function checkAllowedLanguages($attribute, $params, $validator): void
	{
		if ($allowedLanguages = $this->getAllowedLanguagesByVoice($this->uvm_say_voice)) {
			if (!in_array($this->uvm_say_language, $allowedLanguages)) {
				$this->addError('uvm_say_language', 'Allowed languages for the selected voice: ' . implode(',', $allowedLanguages));
			}
		}
	}

    public function isExistVoiceRecordFile(): bool
    {
        $filePath = \Yii::getAlias('@frontend/web/');
        $fileName = $this->uvm_voice_file_message;
        return $fileName && file_exists($filePath . $fileName);
    }

	public function deleteRecord(?string $oldRecord = null): void
	{
		$filePath = \Yii::getAlias('@frontend/web/');
		$fileName = $oldRecord ?: $this->oldAttributes['uvm_voice_file_message'] ?? '';
		if ($fileName && file_exists($filePath . $fileName)) {
			unlink($filePath . $fileName);
		}
	}

	public function getSaveVoiceList(): array
	{
		return self::ALLOWED_SAY_VOICE;
	}

	public function getAllowedList(): array
	{
		$result = [];
		foreach (self::ALLOWED_SAY_VOICE as $item) {
			$result = ArrayHelper::merge($result, $this->getAllowedLanguagesByVoice($item));
		}
		return $result;
	}

	public function delete()
	{
		$this->deleteRecord();
		return parent::delete(); // TODO: Change the autogenerated stub
	}

    public function getResponse(): array
    {
        $response = [
            'say' => [],
            'play' => [],
            'record' => [
                'enabled' => true
            ],
        ];

        if ($this->uvm_say_text_message) {
            $response['say']['message'] = $this->uvm_say_text_message;
            if ($this->uvm_say_language) {
                $response['say']['language'] = $this->uvm_say_language;
            }
            if ($this->uvm_say_voice) {
                $response['say']['voice'] = $this->uvm_say_voice;
            }
        }

        if ($this->uvm_voice_file_message) {
            if ($this->isExistVoiceRecordFile()) {
                if ($url = $this->getVoiceUrl()) {
                    $response['play']['url'] = $url;
                }
            } else {
                Yii::error(VarDumper::dumpAsString([
                    'error' => 'File not exist',
                    'userVoiceMailId' => $this->uvm_id,
                    'voiceFile' => $this->uvm_voice_file_message,
                ]), 'UserVoiceMail');
            }
        }

        if ($this->uvm_record_enable) {
            $response['record']['maxLength'] = $this->uvm_max_recording_time;
        } else {
            $response['record']['enabled'] = false;
        }

        return $response;
    }

    public function getVoiceUrl(): string
    {
        if (!$this->uvm_voice_file_message) {
            return '';
        }
        return Yii::$app->params['url_address'] . $this->uvm_voice_file_message;
    }
}
