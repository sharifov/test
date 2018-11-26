<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "email".
 *
 * @property int $e_id
 * @property int $e_reply_id
 * @property int $e_lead_id
 * @property int $e_project_id
 * @property string $e_email_from
 * @property string $e_email_to
 * @property string $e_email_cc
 * @property string $e_email_bc
 * @property string $e_email_subject
 * @property string $e_email_body_html
 * @property string $e_email_body_text
 * @property string $e_attach
 * @property string $e_email_data
 * @property int $e_type_id
 * @property int $e_template_type_id
 * @property string $e_language_id
 * @property int $e_communication_id
 * @property int $e_is_deleted
 * @property int $e_is_new
 * @property int $e_delay
 * @property int $e_priority
 * @property int $e_status_id
 * @property string $e_status_done_dt
 * @property string $e_read_dt
 * @property string $e_error_message
 * @property int $e_created_user_id
 * @property int $e_updated_user_id
 * @property string $e_created_dt
 * @property string $e_updated_dt
 *
 * @property Employee $eCreatedUser
 * @property Language $eLanguage
 * @property Lead $eLead
 * @property Project $eProject
 * @property EmailTemplateType $eTemplateType
 * @property Employee $eUpdatedUser
 */
class Email extends \yii\db\ActiveRecord
{

    public const STATUS_NEW     = 1;
    public const STATUS_PENDING = 2;
    public const STATUS_PROCESS = 3;
    public const STATUS_CANCEL  = 4;
    public const STATUS_DONE    = 5;
    public const STATUS_ERROR   = 6;

    public const STATUS_LIST = [
        self::STATUS_NEW        => 'New',
        self::STATUS_PENDING    => 'Pending',
        self::STATUS_PROCESS    => 'Process',
        self::STATUS_CANCEL     => 'Cancel',
        self::STATUS_DONE       => 'Done',
        self::STATUS_ERROR      => 'Error',
    ];

    public const PRIORITY_LOW       = 1;
    public const PRIORITY_NORMAL    = 2;
    public const PRIORITY_HIGH      = 3;

    public const PRIORITY_LIST = [
        self::PRIORITY_LOW      => 'Low',
        self::PRIORITY_NORMAL   => 'Normal',
        self::PRIORITY_HIGH     => 'High',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['e_reply_id', 'e_lead_id', 'e_project_id', 'e_type_id', 'e_template_type_id', 'e_communication_id', 'e_is_deleted', 'e_is_new', 'e_delay', 'e_priority', 'e_status_id', 'e_created_user_id', 'e_updated_user_id'], 'integer'],
            [['e_email_from', 'e_email_to'], 'required'],
            [['e_email_body_html', 'e_email_body_text', 'e_email_data'], 'string'],
            [['e_status_done_dt', 'e_read_dt', 'e_created_dt', 'e_updated_dt'], 'safe'],
            [['e_email_from', 'e_email_to', 'e_email_cc', 'e_email_bc', 'e_email_subject', 'e_attach'], 'string', 'max' => 255],
            [['e_language_id'], 'string', 'max' => 5],
            [['e_error_message'], 'string', 'max' => 500],
            [['e_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['e_created_user_id' => 'id']],
            [['e_language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['e_language_id' => 'language_id']],
            [['e_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['e_lead_id' => 'id']],
            [['e_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['e_project_id' => 'id']],
            [['e_template_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmailTemplateType::class, 'targetAttribute' => ['e_template_type_id' => 'etp_id']],
            [['e_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['e_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'e_id' => 'E ID',
            'e_reply_id' => 'E Reply ID',
            'e_lead_id' => 'E Lead ID',
            'e_project_id' => 'E Project ID',
            'e_email_from' => 'E Email From',
            'e_email_to' => 'E Email To',
            'e_email_cc' => 'E Email Cc',
            'e_email_bc' => 'E Email Bc',
            'e_email_subject' => 'E Email Subject',
            'e_email_body_html' => 'E Email Body Html',
            'e_email_body_text' => 'E Email Body Text',
            'e_attach' => 'E Attach',
            'e_email_data' => 'E Email Data',
            'e_type_id' => 'E Type ID',
            'e_template_type_id' => 'E Template Type ID',
            'e_language_id' => 'E Language ID',
            'e_communication_id' => 'E Communication ID',
            'e_is_deleted' => 'E Is Deleted',
            'e_is_new' => 'E Is New',
            'e_delay' => 'E Delay',
            'e_priority' => 'E Priority',
            'e_status_id' => 'E Status ID',
            'e_status_done_dt' => 'E Status Done Dt',
            'e_read_dt' => 'E Read Dt',
            'e_error_message' => 'E Error Message',
            'e_created_user_id' => 'E Created User ID',
            'e_updated_user_id' => 'E Updated User ID',
            'e_created_dt' => 'E Created Dt',
            'e_updated_dt' => 'E Updated Dt',
        ];
    }

    /**
     * @return mixed|string
     */
    public function getStatusName()
    {
        return self::STATUS_LIST[$this->e_status_id] ?? '-';
    }

    /**
     * @return mixed|string
     */
    public function getPriorityName()
    {
        return self::PRIORITY_LIST[$this->e_project_id] ?? '-';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getECreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'e_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getELanguage()
    {
        return $this->hasOne(Language::class, ['language_id' => 'e_language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getELead()
    {
        return $this->hasOne(Lead::class, ['id' => 'e_lead_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEProject()
    {
        return $this->hasOne(Project::class, ['id' => 'e_project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getETemplateType()
    {
        return $this->hasOne(EmailTemplateType::class, ['etp_id' => 'e_template_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'e_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return EmailQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EmailQuery(get_called_class());
    }
}
