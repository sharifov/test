<?php

namespace common\models;

use common\components\CommunicationService;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

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
 * @property string $e_message_id
 * @property string $e_ref_message_id
 * @property string $e_inbox_created_dt
 * @property int $e_inbox_email_id
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
    public const TYPE_DRAFT     = 0;
    public const TYPE_OUTBOX    = 1;
    public const TYPE_INBOX     = 2;

    public const TYPE_LIST = [
        self::TYPE_DRAFT    => 'Draft',
        self::TYPE_OUTBOX   => 'Outbox',
        self::TYPE_INBOX    => 'Inbox',
    ];


    public $quotes = [];

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


    public const FILTER_TYPE_ALL        = 1;
    public const FILTER_TYPE_INBOX      = 2;
    public const FILTER_TYPE_OUTBOX     = 3;
    public const FILTER_TYPE_DRAFT      = 4;
    public const FILTER_TYPE_TRASH      = 5;


    public const FILTER_TYPE_LIST = [
        self::FILTER_TYPE_ALL       => 'ALL',
        self::FILTER_TYPE_INBOX     => 'INBOX',
        self::FILTER_TYPE_OUTBOX    => 'OUTBOX',
        self::FILTER_TYPE_DRAFT     => 'DRAFT',
        self::FILTER_TYPE_TRASH     => 'TRASH',
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
            [['e_reply_id', 'e_lead_id', 'e_project_id', 'e_type_id', 'e_template_type_id', 'e_communication_id', 'e_is_deleted', 'e_is_new', 'e_delay', 'e_priority', 'e_status_id', 'e_created_user_id', 'e_updated_user_id', 'e_inbox_email_id'], 'integer'],
            [['e_email_from', 'e_email_to'], 'required'],
            [['e_email_body_html', 'e_email_body_text', 'e_email_data', 'e_ref_message_id'], 'string'],
            [['e_status_done_dt', 'e_read_dt', 'e_created_dt', 'e_updated_dt', 'e_inbox_created_dt'], 'safe'],
            [['e_email_from', 'e_email_to', 'e_email_cc', 'e_email_bc', 'e_email_subject', 'e_attach', 'e_message_id'], 'string', 'max' => 255],
            [['e_language_id'], 'string', 'max' => 5],
            [['e_error_message'], 'string', 'max' => 500],
            [['e_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['e_created_user_id' => 'id']],
            [['e_language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['e_language_id' => 'language_id']],
            [['e_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['e_lead_id' => 'id']],
            [['e_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['e_project_id' => 'id']],
            [['e_template_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmailTemplateType::class, 'targetAttribute' => ['e_template_type_id' => 'etp_id']],
            [['e_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['e_updated_user_id' => 'id']],
            [['quotes'],'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'e_id' => 'ID',
            'e_reply_id' => 'Reply ID',
            'e_lead_id' => 'Lead ID',
            'e_project_id' => 'Project ID',
            'e_email_from' => 'Email From',
            'e_email_to' => 'To',
            'e_email_cc' => 'Cc',
            'e_email_bc' => 'Bc',
            'e_email_subject' => 'Subject',
            'e_email_body_html' => 'Body Html',
            'e_email_body_text' => 'Body Text',
            'e_attach' => 'Attach',
            'e_email_data' => 'Email Data',
            'e_type_id' => 'Type ID',
            'e_template_type_id' => 'Template Type ID',
            'e_language_id' => 'Language ID',
            'e_communication_id' => 'Communication ID',
            'e_is_deleted' => 'Is Deleted',
            'e_is_new' => 'Is New',
            'e_delay' => 'Delay',
            'e_priority' => 'Priority',
            'e_status_id' => 'Status ID',
            'e_status_done_dt' => 'Status Done Dt',
            'e_read_dt' => 'Read Dt',
            'e_error_message' => 'Error Message',
            'e_created_user_id' => 'Created User ID',
            'e_updated_user_id' => 'Updated User ID',
            'e_created_dt' => 'Created Dt',
            'e_updated_dt' => 'Updated Dt',
            'e_message_id' => 'Message ID',
            'e_ref_message_id' => 'Reference Message ID',
            'e_inbox_created_dt' => 'Inbox Created Dt',
            'e_inbox_email_id' => 'Inbox Email ID',
        ];
    }



    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['e_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['e_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            /*'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'e_created_user_id',
                'updatedByAttribute' => 'e_updated_user_id',
            ],*/
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
     * @return mixed|string
     */
    public function getTypeName()
    {
        return self::TYPE_LIST[$this->e_type_id] ?? '-';
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

    public function setEmailData($emailData)
    {
        $this->e_email_data = json_encode($emailData);
    }

    public function getEmailData()
    {
        return json_decode($this->e_email_data, true);
    }

    public function setQuotes($quotes)
    {
        $this->quotes = implode(',', $quotes);
    }

    public function getQuotes()
    {
        return explode(',',$this->quotes);
    }

    /**
     * @param $text
     * @return mixed
     */
    public static function strip_html_tags($text )
    {
        $text = preg_replace(
            [
                // Remove invisible content
                '@<head[^>]*?>.*?</head>@siu',
                '@<style[^>]*?>.*?</style>@siu',
                '@<script[^>]*?.*?</script>@siu',
                '@<object[^>]*?.*?</object>@siu',
                '@<embed[^>]*?.*?</embed>@siu',
                '@<applet[^>]*?.*?</applet>@siu',
                '@<noframes[^>]*?.*?</noframes>@siu',
                '@<noscript[^>]*?.*?</noscript>@siu',
                '@<noembed[^>]*?.*?</noembed>@siu',
                // Add line breaks before and after blocks
                '@</?((address)|(blockquote)|(center)|(del))@iu',
                '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
                '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
                '@</?((table)|(th)|(td)|(caption))@iu',
                '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
                '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
                '@</?((frameset)|(frame)|(iframe))@iu',
            ],
            [
                ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
                "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
                "\n\$0", "\n\$0",
            ],
            $text );

        $text = strip_tags( $text );
        $text = preg_replace('!\s+!', ' ', $text);

        return $text;
    }

    /**
     * @return bool
     * @throws \yii\httpclient\Exception
     */
    public function sendMail(): bool
    {
        /** @var CommunicationService $communication */
        $communication = Yii::$app->communication;
        $data = [];
        $data['project_id'] = $this->e_project_id;

        $content_data['email_body_html'] = $this->e_email_body_html;
        $content_data['email_body_text'] = $this->e_email_body_text;
        $content_data['email_subject'] = $this->e_email_subject;
        $content_data['email_reply_to'] = $this->e_email_from;
        $content_data['email_cc'] = $this->e_email_cc;
        $content_data['email_bcc'] = $this->e_email_bc;

        $request = $communication->mailSend($this->e_project_id, 'cl_offer', $this->e_email_from, $this->e_email_to, $content_data, $data, ($this->e_language_id ?: 'en-US'), 0);

        if($request && $request['data']) {
            $this->e_status_id = $request['data']['eq_status_id'];
            $this->save();
            return true;
        }
        // VarDumper::dump($request, 10, true); exit;

        return false;
    }

    /**
     * @return string
     */
    public function generateMessageId(): string
    {
        $message = uniqid().'.'.$this->e_email_from;
        return $message;
    }
}
