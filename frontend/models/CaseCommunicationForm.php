<?php
namespace frontend\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\Call;
use common\models\EmailTemplateType;
use common\models\Employee;
use common\models\Language;
use common\models\Lead;
use common\models\SmsTemplateType;
use sales\entities\cases\Cases;
use yii\base\Model;

/**
 * Class CaseCommunicationForm
 * @package frontend\models
 *
 * @property integer $c_type_id
 * @property integer $c_case_id
 * @property string $c_phone_number
 * @property integer $c_sms_tpl_id
 * @property string $c_sms_tpl_key
 * @property string $c_sms_message
 * @property string $c_email_to
 * @property string $c_email_subject
 * @property string $c_email_message
 * @property integer $c_email_tpl_id
 * @property string $c_email_tpl_key
 * @property integer $c_user_id
 * @property string $c_language_id
 * @property string $c_quotes
 * @property integer $c_call_id
 *
 * @property array $quoteList;
 *
 * @property integer $c_preview_email
 * @property integer $c_preview_sms
 * @property integer $c_voice_status
 * @property string $c_voice_sid
 *
 */

class CaseCommunicationForm extends Model
{

    public const TYPE_EMAIL = 1;
    public const TYPE_SMS   = 2;
    public const TYPE_VOICE = 3;


    public const TYPE_LIST = [
        self::TYPE_EMAIL    => 'Email',
        self::TYPE_SMS      => 'SMS',
        self::TYPE_VOICE    => 'Phone',
    ];

    public const TPL_TYPE_EMAIL_BLANK       = 8;


    public const TPL_TYPE_EMAIL_OFFER_KEY       = 'cl_offer';
    public const TPL_TYPE_EMAIL_OFFER_VIEW_KEY  = 'offer_quote_view';
    public const TPL_TYPE_EMAIL_BLANK_KEY       = 'cl_agent_blank';

    public const TPL_TYPE_SMS_OFFER_KEY         = 'sms_client_offer';
    public const TPL_TYPE_SMS_OFFER_VIEW_KEY    = 'sms_client_offer_view';
    public const TPL_TYPE_SMS_BLANK_KEY         = 'sms_agent_blank';



    public $c_type_id;
    public $c_case_id;

    public $c_phone_number;

    public $c_sms_tpl_id;
    public $c_sms_tpl_key;
    public $c_sms_message;

    public $c_email_to;
    public $c_email_subject;
    public $c_email_message;
    public $c_email_tpl_id;
    public $c_email_tpl_key;

    public $c_user_id;
    public $c_language_id;

    public $c_preview_email;
    public $c_preview_sms;
    public $c_voice_status;
    public $c_voice_sid;
    public $c_call_id;

    public $c_quotes;

    public $quoteList;



    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            [['c_type_id', 'c_case_id'], 'required'],

            [['c_email_to', 'c_language_id', 'c_email_tpl_key'], 'required', 'when' => function (self $model) {
                return (int) $model->c_type_id === self::TYPE_EMAIL;
            },
                'whenClient' => "function (attribute, value) { return $('#c_type_id').val() == " . self::TYPE_EMAIL . '; }'
            ],


            [['c_email_message', 'c_email_subject'], 'required', 'when' => function (self $model) {
                return $model->c_email_tpl_key === self::TPL_TYPE_EMAIL_BLANK_KEY && (int) $model->c_type_id === self::TYPE_EMAIL;
            },
                'whenClient' => "function (attribute, value) { return ($('#c_type_id').val() == " . self::TYPE_EMAIL . " && $('#c_email_tpl_key').val() == '" . self::TPL_TYPE_EMAIL_BLANK_KEY . "'); }"
            ],


            [['c_phone_number', 'c_sms_tpl_key'], 'required', 'when' => function (self $model) {
                return (int) $model->c_type_id === self::TYPE_SMS;
            },
                'whenClient' => "function (attribute, value) { return $('#c_type_id').val() == " . self::TYPE_SMS . '; }'
            ],


            /*[['c_sms_message'], 'required', 'when' => function (CommunicationForm $model) {
                return $model->c_sms_tpl_id != self::TPL_TYPE_SMS_OFFER && $model->c_type_id == self::TYPE_SMS;
            },
                'whenClient' => "function (attribute, value) { return $('#c_type_id').val() == " . self::TYPE_SMS . " && $('#c_sms_tpl_id').val() != " . self::TPL_TYPE_SMS_OFFER . '; }'
            ],*/


            [['c_phone_number'], 'required', 'when' => function (self $model) {
                return (int) $model->c_type_id === self::TYPE_VOICE;
            },
                'whenClient' => "function (attribute, value) {
                    return $('#c_type_id').val() == " . self::TYPE_VOICE . '; }'
            ],



            [['c_quotes'], 'required', 'when' => function (self $model) {
                return ($model->c_email_tpl_key === self::TPL_TYPE_EMAIL_OFFER_KEY || $model->c_email_tpl_key === self::TPL_TYPE_EMAIL_OFFER_VIEW_KEY) && (int) $model->c_type_id === self::TYPE_EMAIL;
            },
                'whenClient' => "function (attribute, value) { return ($('#c_type_id').val() == " . self::TYPE_EMAIL . " && ($('#c_email_tpl_key').val() == '" . self::TPL_TYPE_EMAIL_OFFER_VIEW_KEY . "' || $('#c_email_tpl_key').val() == '" . self::TPL_TYPE_EMAIL_OFFER_KEY . "')); }"
            ],

            [['c_quotes'], 'required', 'when' => function (self $model) {
                return ($model->c_sms_tpl_key === self::TPL_TYPE_SMS_OFFER_KEY || $model->c_sms_tpl_key === self::TPL_TYPE_SMS_OFFER_VIEW_KEY) && (int) $model->c_type_id === self::TYPE_SMS;
            },
                'whenClient' => "function (attribute, value) { return $('#c_type_id').val() == " . self::TYPE_SMS . " && ($('#c_sms_tpl_key').val() == '" . self::TPL_TYPE_SMS_OFFER_KEY . "' || $('#c_sms_tpl_key').val() == '" . self::TPL_TYPE_SMS_OFFER_VIEW_KEY . "'); }"
            ],


            [['c_email_subject', 'c_email_message', 'c_sms_message'], 'trim'],

            [['c_phone_number'], 'string', 'max' => 30],
            [['c_phone_number'], PhoneInputValidator::class],

            [['c_voice_sid'], 'string', 'max' => 40],


            //[['c_type_id'], 'validateType'],

            [['c_email_to'], 'email'],
            [['c_sms_tpl_id', 'c_email_tpl_id', 'c_user_id', 'c_type_id', 'c_case_id', 'c_voice_status', 'c_call_id'], 'integer'],

            [['c_email_message', 'c_sms_message'], 'string'],
            [['c_sms_tpl_key', 'c_email_tpl_key'], 'string', 'max' => 50],
            [['c_sms_message'], 'string', 'max' => 500],
            [['c_email_subject'], 'string', 'max' => 80, 'min' => 5],

            [['c_language_id'], 'string', 'max' => 5],

            [['c_quotes'], 'string'], //'each', 'rule' => ['integer']],
            [['c_quotes'], 'validateQuotes'],

            [['c_email_tpl_key'], 'validateEmailTemplateKey'],
            [['c_sms_tpl_key'], 'validateSmsTemplateKey'],

            [['c_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['c_user_id' => 'id']],
            [['c_language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['c_language_id' => 'language_id']],
            [['c_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['c_case_id' => 'cs_id']],

            [['c_email_tpl_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmailTemplateType::class, 'targetAttribute' => ['c_email_tpl_id' => 'etp_id']],
            [['c_sms_tpl_id'], 'exist', 'skipOnError' => true, 'targetClass' => SmsTemplateType::class, 'targetAttribute' => ['c_sms_tpl_id' => 'stp_id']],
            [['c_call_id'], 'exist', 'skipOnError' => true, 'targetClass' => Call::class, 'targetAttribute' => ['c_call_id' => 'c_id']],



        ];
    }

    /**
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function validateQuotes($attribute, $params, $validator): void
    {
        if (!empty($this->c_quotes)) {
            $this->quoteList = @json_decode($this->c_quotes, true);
            if(!is_array($this->quoteList)) {
                $this->quoteList = [];
            }
        }
    }

    /**
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function validateEmailTemplateKey($attribute, $params, $validator): void
    {
        $tpl = EmailTemplateType::find()->where(['etp_key' => $this->$attribute])->limit(1)->one();
        if (!$tpl) {
            $this->addError($attribute, 'Not exist this Email Template (DB)');
        } else {
            $this->c_email_tpl_id = $tpl->etp_id;
        }
    }

    /**
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function validateSmsTemplateKey($attribute, $params, $validator): void
    {
        $tpl = SmsTemplateType::find()->where(['stp_key' => $this->$attribute])->limit(1)->one();
        if (!$tpl) {
            $this->addError($attribute, 'Not exist this SMS Template (DB)');
        } else {
            $this->c_sms_tpl_id = $tpl->stp_id;
        }
    }


    /**
     * @return array
     */
    public function attributeLabels() : array
    {
        return [
            'c_type_id'         => 'Message Type',
            'c_lead_id'         => 'Lead Id',
            'c_sms_tpl_id'      => 'SMS Template',
            'c_sms_tpl_key'      => 'SMS Template',
            'c_sms_message'     => 'SMS Message',
            'c_email_to'        => 'Email',
            'c_email_tpl_id'    => 'Email Template',
            'c_email_tpl_key'    => 'Email Template',
            'c_email_message'   => 'Email Message',
            'c_email_subject'   => 'Subject',
            'c_phone_number'    => 'Phone number',
            'c_language_id'     => 'Language',
            'c_user_id'         => 'Agent ID',
            'c_quotes'          => 'Checked Quotes'
        ];
    }

}