<?php
namespace frontend\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\EmailTemplateType;
use common\models\Employee;
use common\models\Language;
use common\models\Lead;
use common\models\SmsTemplateType;
use yii\base\Model;

/**
 * Class CommunicationForm
 * @package frontend\models
 *
 * @property integer $c_type_id
 * @property integer $c_lead_id
 * @property string $c_phone_number
 * @property integer $c_sms_tpl_id
 * @property string $c_sms_message
 * @property string $c_email_to
 * @property string $c_email_subject
 * @property string $c_email_message
 * @property integer $c_email_tpl_id
 * @property integer $c_user_id
 * @property string $c_language_id
 */

class CommunicationForm extends Model
{

    public const TYPE_EMAIL = 1;
    public const TYPE_SMS   = 2;
    public const TYPE_VOICE = 3;

    public const TYPE_LIST = [
        self::TYPE_EMAIL    => 'Email',
        self::TYPE_SMS      => 'SMS',
        self::TYPE_VOICE    => 'Phone',
    ];

    public $c_type_id;
    public $c_lead_id;

    public $c_phone_number;

    public $c_sms_tpl_id;
    public $c_sms_message;

    public $c_email_to;
    public $c_email_subject;
    public $c_email_message;
    public $c_email_tpl_id;

    public $c_user_id;
    public $c_language_id;


    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            [['c_type_id', 'c_lead_id'], 'required'],

            [['c_email_subject', 'c_email_message', 'c_sms_message'], 'trim'],

            [['c_phone_number'], 'string', 'max' => 30],
            [['c_phone_number'], PhoneInputValidator::class],
            [['c_email_to'], 'email'],
            [['c_sms_tpl_id', 'c_email_tpl_id', 'c_user_id', 'c_type_id', 'c_lead_id'], 'integer'],

            [['c_email_message', 'c_sms_message'], 'string'],
            [['c_sms_message'], 'string', 'max' => 500],
            [['c_email_subject'], 'string', 'max' => 80],

            [['c_language_id'], 'string', 'max' => 5],

            [['c_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['c_user_id' => 'id']],
            [['c_language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['c_language_id' => 'language_id']],
            [['c_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['c_lead_id' => 'id']],

            [['c_email_tpl_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmailTemplateType::class, 'targetAttribute' => ['c_email_tpl_id' => 'etp_id']],
            [['c_sms_tpl_id'], 'exist', 'skipOnError' => true, 'targetClass' => SmsTemplateType::class, 'targetAttribute' => ['c_sms_tpl_id' => 'stp_id']],
        ];
    }


    /**
     * @return array
     */
    public function attributeLabels() : array
    {
        return [
            'c_type_id'            => 'Message Type',
            'c_lead_id'         => 'Lead Id',
            'c_sms_tpl_id'      => 'SMS Template',
            'c_sms_message'     => 'SMS Message',
            'c_email_to'        => 'Email',
            'c_email_tpl_id'    => 'Email Template',
            'c_email_message'   => 'Email Message',
            'c_email_subject'   => 'Subject',
            'c_phone_number'    => 'Phone number',
            'c_language_id'     => 'Language ID',
            'c_user_id'         => 'Agent ID',
        ];
    }

}