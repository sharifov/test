<?php

namespace modules\twilio\src\entities;

use common\models\Language;
use common\models\Project;
use common\models\Sms;
use common\models\SmsTemplateType;
use sales\model\phoneList\entity\PhoneList;
use yii\base\Model;

/**
 * Class SmsForm
 * @package modules\twilio\src\entities
 *
 * @property int $sq_project_id
 * @property string $sq_phone_from
 * @property string $sq_phone_to
 * @property string $sq_sms_text
 * @property string $sq_sms_data
 * @property int $sq_type_id
 * @property string $sq_type_key
 * @property string $type_key
 * @property string $sq_language_id
 * @property int $sq_delay
 */
class SmsForm extends Model
{
	public $sq_project_id;
	public $sq_phone_from;
	public $sq_phone_to;
	public $sq_sms_text;
	public $sq_sms_data;
	public $sq_type_id;
	public $sq_type_key;
	public $sq_language_id;
	public $sq_delay;
	public $s_id;

	public function formName() : string
	{
		return 'sms';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{

		return [
			//[['lead_id'], 'required', 'on' => [self::SCENARIO_UPDATE, self::SCENARIO_GET]],
			//[['adults', 'flights'], 'required', 'except' => [self::SCENARIO_UPDATE, self::SCENARIO_GET]],
			[['sq_phone_from', 'sq_phone_to', 'sq_project_id', 'sq_language_id', 'sq_sms_data'], 'required'], //'sq_type_key',
			//[['sq_project_id', 'sq_type_id'], 'default', 'value' => null],
			[['sq_delay'], 'default', 'value' => 0],
			[['sq_project_id', 'sq_type_id', 'sq_delay'], 'integer'],
			[['sq_sms_text'], 'string'],
			[['sq_phone_from', 'sq_phone_to'], 'string', 'max' => 30],
			[['sq_language_id'], 'string', 'max' => 5],
			[['sq_type_key'], 'string', 'max' => 100],
			[['sq_type_key'], 'checkIsType'],
			[['sq_phone_from'], 'checkPhoneFrom'],
			[['sq_sms_data'], 'safe'],
			[['sq_type_id'], 'in', 'skipOnError' => true, 'range' => array_keys(Sms::getSmsTypeList())],
			[['sq_language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['sq_language_id' => 'language_id']],
			[['sq_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['sq_project_id' => 'id']],
		];
	}

	public function attributeLabels() : array
	{
		return [
			'sq_id' => 'ID',
			'sq_project_id' => 'Project ID',
			'sq_delay' => 'Delay',
			'sq_phone_from' => 'Phone number From',
			'sq_phone_to' => 'Phone number To',
			'sq_sms_text' => 'SMS Text',
			'sq_sms_data' => 'SMS Data',
			'sq_type_id' => 'Type ID',
			'sq_type_key'   => 'Template Type Key',
			'sq_language_id' => 'Language ID',
		];
	}

	public function checkIsType($attribute, $params) : void
	{
		if (empty($this->sq_type_key)) {
			//$this->addError('sq_type_key', 'SMS Template Type key cannot be empty');
		} else {
			if($type = SmsTemplateType::find()->where(['stp_key' => $this->sq_type_key])->one()) {
				$this->sq_type_id = $type->stp_id;
			} else {
				$this->addError('sq_type_key ', 'Not found SMS Template Key "'.$this->sq_type_key.'" in DB');
			}
		}
	}

	public function checkPhoneFrom($attribute, $params) : void
	{
		$phoneExist = PhoneList::find()->where(['pl_phone_number' => $this->sq_phone_from])->exists();
		if(!$phoneExist) {
			$this->addError('sq_phone_from ', 'Unknown phone number "'.$this->sq_phone_from.'"');
		}

	}
}