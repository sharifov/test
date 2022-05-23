<?php

namespace frontend\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\EmailTemplateType;
use common\models\Employee;
use common\models\Language;
use common\models\Lead;
use common\models\SmsTemplateType;
use src\model\sms\useCase\send\fromLead\AbacSmsFromNumberList;
use yii\base\Model;

/**
 * Class LeadPreviewSmsForm
 * @package frontend\models
 *
 * @property integer $s_lead_id
 * @property string $s_phone_from
 * @property string $s_phone_to
 * @property string $s_sms_message
 * @property integer $s_sms_tpl_id
 * @property integer $s_user_id
 * @property string $s_language_id
 *
 * @property string $s_quote_list
 * @property boolean $is_send
 *
 * @property AbacSmsFromNumberList $smsFromNumberList
 *
 */
class LeadPreviewSmsForm extends Model
{
    public $s_lead_id;
    public $s_phone_to;
    public $s_phone_from;
    public $s_sms_message;
    public $s_sms_tpl_id;
    public $s_user_id;
    public $s_language_id;
    public $s_quote_list;

    public $s_qc_uid;

    public $is_send;

    private AbacSmsFromNumberList $smsFromNumberList;

    public function __construct(AbacSmsFromNumberList $smsFromNumberList, $config = [])
    {
        parent::__construct($config);
        $this->smsFromNumberList = $smsFromNumberList;
    }


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['s_lead_id', 's_phone_from', 's_phone_to', 's_sms_message', 's_qc_uid'], 'required'],
            [['s_sms_message'], 'trim'],
            //[['s_type_id'], 'validateType'],

            [['s_sms_tpl_id', 's_lead_id', 's_user_id'], 'integer'],
            [['s_sms_message'], 'string', 'min' => 10],

            [['s_quote_list', 's_qc_uid'], 'string'],

            [['s_phone_to', 's_phone_from'], 'string', 'max' => 30],
            [['s_phone_to', 's_phone_from'], PhoneInputValidator::class, 'enableClientValidation' => false],

            [['s_language_id'], 'string', 'max' => 5],
            [['s_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['s_user_id' => 'id']],
            [['s_language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['s_language_id' => 'language_id']],
            [['s_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['s_lead_id' => 'id']],
            [['s_sms_tpl_id'], 'exist', 'skipOnError' => true, 'targetClass' => SmsTemplateType::class, 'targetAttribute' => ['s_sms_tpl_id' => 'stp_id']],

            ['s_phone_from', function () {
                if (!$this->smsFromNumberList->isExist($this->s_phone_from)) {
                    $this->addError('s_phone_from', 'Phone Number From is invalid');
                }
            }, 'skipOnError' => true, 'skipOnEmpty' => true],
        ];
    }

    /*public function validateType($attribute, $params, $validator)
    {
        if ($this->$attribute == self::TYPE_SMS) {
            //if()   $this->addError($attribute, 'The country must be either "USA" or "Indonesia".');
        }
    }*/


    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            's_lead_id' => 'Lead Id',
            's_phone_to' => 'Phone Number To',
            's_phone_from' => 'Phone Number From',
            's_sms_tpl_id' => 'SMS Template',
            's_sms_message' => 'SMS Message',
            's_language_id' => 'Language',
            's_user_id' => 'Agent ID',
            's_qc_uid' => 'Quote Communication UID'
        ];
    }
}
