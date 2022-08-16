<?php

namespace common\models;

use common\components\ChartTools;
use common\components\CommunicationService;
use common\components\jobs\UserTaskCompletionJob;
use common\models\query\EmailQuery;
use common\models\DepartmentEmailProject;
use common\models\UserProjectParams;
use DateTime;
use modules\featureFlag\FFlag;
use modules\lead\src\services\LeadTaskListService;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\TaskObject;
use src\auth\Auth;
use src\behaviors\metric\MetricEmailCounterBehavior;
use src\entities\cases\Cases;
use src\helpers\app\AppHelper;
use src\helpers\email\TextConvertingHelper;
use src\model\leadBusinessExtraQueue\service\LeadBusinessExtraQueueService;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogQuery;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogStatus;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\leadUserData\entity\LeadUserData;
use src\model\leadUserData\entity\LeadUserDataDictionary;
use src\model\leadUserData\repository\LeadUserDataRepository;
use src\services\abtesting\email\EmailTemplateOfferABTestingService;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use src\entities\email\Email as EmailNormalized;
use src\entities\email\helpers\EmailType;
use src\helpers\email\MaskEmailHelper;
use src\services\email\EmailServiceHelper;
use src\entities\email\EmailInterface;
use src\entities\EventTrait;
use src\entities\email\helpers\EmailStatus;

/**
 * This is the model class for table "email".
 *
 * @property int $e_id
 * @property int $e_reply_id
 * @property int|null $e_lead_id
 * @property int $e_project_id
 * @property string $e_email_from
 * @property string $e_email_to
 * @property string $e_email_cc
 * @property string $e_email_bc
 * @property string $e_email_subject
 * @property string $e_email_body_text
 * @property string $e_email_body_html // please don't use.
 * @property string $e_email_body_blob // If get uncompressed html - please use getEmailBodyHtml()
 * @property string $e_attach
 * @property string $e_email_data
 * @property int $e_type_id
 * @property int $e_template_type_id
 * @property string $e_language_id
 * @property int $e_communication_id
 * @property bool $e_is_deleted
 * @property bool $e_is_new
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
 * @property string $e_email_from_name
 * @property string $e_email_to_name
 * @property int|null $e_case_id
 * @property int|null $e_client_id
 *
 * @property string $body_html
 *
 * @property Employee $createdUser
 * @property Employee $updatedUser
 * @property Client $client
 * @property Cases $case
 * @property Language $language
 * @property Lead $lead
 * @property Project $project
 * @property EmailTemplateType $templateType
 *
 * @property array $usersIdByEmail
 * @property string|mixed $priorityName
 * @property int|null $leadId
 * @property int|null $caseId
 * @property int|null $clientId
 * @property int|null $projectId
 * @property int|null $templateTypeId
 * @property string|null $templateTypeName
 * @property string|null $emailFrom
 * @property string|null $emailFromName
 * @property string|null $emailTo
 * @property string|null $emailToName
 * @property string|null $templateTypeName
 * @property string|null $emailSubject
 * @property int|null $communicationId
 * @property string|null $languageId
 * @property int|null $departmentId
 * @property string|null $emailBodyHtml
 * @property array|null $emailData
 * @property string|null $errorMessage
 * @property string|null $statusDoneDt
 * @property string $statusName
 * @property string $typeName
 * @property bool $isNew
 * @property bool $isDeleted
 *
 */
class Email extends \yii\db\ActiveRecord implements EmailInterface
{
    use EventTrait;

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
    public const STATUS_REVIEW  = 7;

    public const STATUS_LIST = [
        self::STATUS_NEW        => 'New',
        self::STATUS_PENDING    => 'Pending',
        self::STATUS_PROCESS    => 'Process',
        self::STATUS_CANCEL     => 'Cancel',
        self::STATUS_DONE       => 'Done',
        self::STATUS_ERROR      => 'Error',
        self::STATUS_REVIEW     => 'Review'
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

    public $body_html;

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
            [['e_reply_id', 'e_lead_id', 'e_project_id', 'e_type_id', 'e_template_type_id', 'e_communication_id', 'e_is_deleted', 'e_priority', 'e_status_id', 'e_created_user_id', 'e_updated_user_id', 'e_inbox_email_id', 'e_case_id'], 'integer'],
            [['e_is_new', 'e_is_deleted'], 'boolean'],
            [['e_email_from', 'e_email_to'], 'required'],
            [['e_email_body_blob', 'e_email_data', 'e_ref_message_id', 'body_html'], 'string'],
            [['e_status_done_dt', 'e_read_dt', 'e_created_dt', 'e_updated_dt', 'e_inbox_created_dt'], 'safe'],
            [['e_email_from', 'e_email_to', 'e_email_cc', 'e_email_bc', 'e_email_subject', 'e_attach', 'e_email_from_name', 'e_email_to_name'], 'string', 'max' => 255],
            ['e_message_id', 'string', 'max' => 500],
            [['e_language_id'], 'string', 'max' => 5],
            [['e_error_message'], 'string', 'max' => 500],
            [['e_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['e_case_id' => 'cs_id']],
            [['e_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['e_created_user_id' => 'id']],
            [['e_language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['e_language_id' => 'language_id']],
            [['e_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['e_lead_id' => 'id']],
            [['e_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['e_project_id' => 'id']],
            [['e_template_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmailTemplateType::class, 'targetAttribute' => ['e_template_type_id' => 'etp_id']],
            [['e_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['e_updated_user_id' => 'id']],
            [['e_client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['e_client_id' => 'id']],
            [['quotes'], 'safe'],
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
            'e_email_from_name' => 'Email From Name',
            'e_email_to' => 'To',
            'e_email_to_name' => 'Email To Name',
            'e_email_cc' => 'Cc',
            'e_email_bc' => 'Bc',
            'e_email_subject' => 'Subject',
            'e_email_body_blob' => 'Body Html',
            'body_html' => 'Body Html',
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
            'e_case_id' => 'Case ID',
            'e_client_id' => 'Client ID',
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
            'metric' => [
                'class' => MetricEmailCounterBehavior::class,
            ],
        ];
    }

    public function isInbox(): bool
    {
        return $this->e_type_id === self::TYPE_INBOX;
    }

    public function isOutbox(): bool
    {
        return $this->e_type_id === self::TYPE_OUTBOX;
    }

    public function getStatusName(): string
    {
        return EmailStatus::getName($this->e_status_id) ?? '-';
    }

    public function getPriorityName(): string
    {
        return self::PRIORITY_LIST[$this->e_priority] ?? '-';
    }

    public function getTypeName(): string
    {
        return EmailType::getName($this->e_type_id) ?? '-';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCase(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'e_case_id']);
    }

    public function getCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'e_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::class, ['language_id' => 'e_language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'e_lead_id']);
    }

    public function getProject(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'e_project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateType()
    {
        return $this->hasOne(EmailTemplateType::class, ['etp_id' => 'e_template_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'e_updated_user_id']);
    }

    public function getClient(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'e_client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNormalized()
    {
        return $this->hasOne(EmailNormalized::class, ['e_id' => 'e_id']);
    }

    public function getTemplateTypeName(): ?string
    {
        return $this->templateType->etp_name ?? null;
    }

    /**
     * {@inheritdoc}
     * @return EmailQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EmailQuery(static::class);
    }

    public function setEmailData($emailData)
    {
        $this->e_email_data = json_encode($emailData);
    }

    public function getEmailData()
    {
        return json_decode($this->e_email_data, true);
    }

    public function updateEmailData($emailData)
    {
        $this->updateAttributes(['e_email_data' => json_encode($emailData)]);
        return $this;
    }

    public function setQuotes($quotes)
    {
        $this->quotes = implode(',', $quotes);
    }

    public function getQuotes()
    {
        return explode(',', $this->quotes);
    }

    /**
     * @deprecated. use through EmailMainService
     */
    public function sendMail(array $data = []): array
    {
        $out = ['error' => false];

        /** @var CommunicationService $communication */
        $communication = Yii::$app->comms;
        $data['project_id'] = $this->e_project_id;

        $content_data['email_body_html'] = $this->getEmailBodyHtml();
        $content_data['email_body_text'] = $this->e_email_body_text;
        $content_data['email_subject'] = $this->e_email_subject;
        $content_data['email_reply_to'] = $this->e_email_from;
        $content_data['email_cc'] = $this->e_email_cc;
        $content_data['email_bcc'] = $this->e_email_bc;

        if ($this->e_email_from_name) {
            $content_data['email_from_name'] = $this->e_email_from_name;
        }

        if ($this->e_email_to_name) {
            $content_data['email_to_name'] = $this->e_email_to_name;
        }

        if ($this->e_message_id) {
            $content_data['email_message_id'] = $this->e_message_id;
        }

        $tplType = $this->templateType ? $this->templateType->etp_key : null;

        try {
            $request = $communication->mailSend($this->e_project_id, $tplType, $this->e_email_from, $this->e_email_to, $content_data, $data, ($this->e_language_id ?: 'en-US'), 0);


            if ($request && isset($request['data']['eq_status_id'])) {
                $this->e_status_id = $request['data']['eq_status_id'];
                $this->e_communication_id = $request['data']['eq_id'];
                $this->save();
            }

            //VarDumper::dump($request, 10, true); exit;

            if ($request && isset($request['error']) && $request['error']) {
                $this->e_status_id = self::STATUS_ERROR;
                $errorData = @json_decode($request['error'], true);
                $this->e_error_message = 'Communication error: ' . ($errorData['message'] ?: $request['error']);
                $this->save();
                $out['error'] = $this->e_error_message;
            }
            /** @fflag FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES, A/B testing for email offer templates enable/disable */
            if ($this->e_status_id !== self::STATUS_ERROR && Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES)) {
                if ($this->e_template_type_id && $this->e_project_id && isset($this->lead)) {
                    EmailTemplateOfferABTestingService::incrementCounterByTemplateAndProjectIds(
                        $this->e_template_type_id,
                        $this->e_project_id,
                        $this->lead->l_dep_id
                    );
                }
            }
            if ($this->e_id && $this->e_lead_id && LeadPoorProcessingService::checkEmailTemplate($tplType)) {
                LeadPoorProcessingService::addLeadPoorProcessingRemoverJob(
                    $this->e_lead_id,
                    [
                        LeadPoorProcessingDataDictionary::KEY_NO_ACTION,
                        LeadPoorProcessingDataDictionary::KEY_EXPERT_IDLE,
                        LeadPoorProcessingDataDictionary::KEY_SEND_SMS_OFFER,
                    ],
                    LeadPoorProcessingLogStatus::REASON_EMAIL
                );
                $lead = $this->lead;
                if (
                    \Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BEQ_ENABLE)
                    && isset($lead)
                    && $lead->isBusinessType()
                    && LeadBusinessExtraQueueLogQuery::isLeadWasInBusinessExtraQueue($lead->id)
                ) {
                    try {
                        LeadBusinessExtraQueueService::addLeadBusinessExtraQueueRemoverJob(
                            $this->e_lead_id,
                            LeadBusinessExtraQueueLogStatus::REASON_SENT_EMAIL
                        );
                    } catch (\RuntimeException | \DomainException $throwable) {
                        $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['callId' => $this->callId]);
                        \Yii::warning($message, 'Email:addLeadBusinessExtraQueueRemoverJob:Exception');
                    } catch (\Throwable $throwable) {
                        $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['callId' => $this->callId]);
                        \Yii::error($message, 'Email:addLeadBusinessExtraQueueRemoverJob:Throwable');
                    }
                }

                if (($lead = $this->lead) && $lead->employee_id && $lead->isProcessing()) {
                    try {
                        $leadUserData = LeadUserData::create(
                            LeadUserDataDictionary::TYPE_EMAIL_OFFER,
                            $lead->id,
                            $lead->employee_id,
                            (new \DateTimeImmutable())
                        );
                        (new LeadUserDataRepository($leadUserData))->save(true);
                    } catch (\RuntimeException | \DomainException $throwable) {
                        $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['emailId' => $this->e_id]);
                        \Yii::warning($message, 'Email:LeadUserData:Exception');
                    } catch (\Throwable $throwable) {
                        $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['emailId' => $this->e_id]);
                        \Yii::error($message, 'Email:LeadUserData:Throwable');
                    }
                }
            }

            if ($this->e_id && ($lead = $this->lead) && (new LeadTaskListService($lead))->isProcessAllowed()) {
                $job = new UserTaskCompletionJob(
                    TargetObject::TARGET_OBJ_LEAD,
                    $lead->id,
                    TaskObject::OBJ_EMAIL,
                    $this->e_id,
                    Auth::id()
                );
                Yii::$app->queue_job->push($job);
            }
        } catch (\Throwable $exception) {
            $error = VarDumper::dumpAsString($exception->getMessage());
            $out['error'] = $error;
            Yii::error($error, 'Email:sendMail:mailSend:exception');
            $this->e_error_message = 'Communication error: ' . $error;
            $this->save();
        }

        //VarDumper::dump($request, 10, true); exit;

        // VarDumper::dump($request, 10, true); exit;

        return $out;
    }

    /**
     * @return string
     */
    public function generateMessageId(): string
    {
        $arr[] = 'kiv';
        $arr[] = $this->e_id;
        $arr[] = $this->e_project_id;
        $arr[] = $this->e_lead_id;
        $arr[] = $this->e_email_from;
        $arr[] = $this->e_case_id;

        $message = '<' . implode('.', $arr) . '>';
        return $message;
    }

    /**
     * @deprecated
     * @return int|mixed
     */
    public function detectLeadId()
    {

        // $subject = 'RE Hello [lid:78456123]';
        // $subject = 'RE Hello [uid:lkasdjlkjkl234]';
        // $ref_message_id = '<kiv.1.6.345.alex.connor@gmail.com> <qwewqeqweqwe.qweqwe@mail.com> <aasdfkjal.sfasldfkl@gmail.com>';

//        $subject = $this->e_email_subject;
//
//        $matches = [];
//        $lead = null;
//
//        preg_match('~\[lid:(\d+)\]~si', $subject, $matches);
//
//        if(isset($matches[1]) && $matches[1]) {
//            $lead_id = (int) $matches[1];
//            $lead = Lead::find()->where(['id' => $lead_id])->one();
//        }
//
//        if(!$lead) {
//            $matches = [];
//            preg_match('~\[uid:(\w+)\]~si', $subject, $matches);
//            if(isset($matches[1]) && $matches[1]) {
//                $lead = Lead::find()->where(['uid' => $matches[1]])->one();
//            }
//        }
//
//        //preg_match('~\[uid:(\w+)\]~si', $subject, $matches);
//
//        if(!$lead) {
//            $matches = [];
//            preg_match_all('~<kiv\.(.+)>~iU', $this->e_ref_message_id, $matches);
//            if (isset($matches[1]) && $matches[1]) {
//                foreach ($matches[1] as $messageId) {
//                    $messageArr = explode('.', $messageId);
//                    if (isset($messageArr[2]) && $messageArr[2]) {
//                        $lead_id = (int) $messageArr[2];
//
//                        $lead = Lead::find()->where(['id' => $lead_id])->one();
//                        if($lead) {
//                            break;
//                        }
//                    }
//                }
//            }
//        }
//
//        if($lead) {
//            $this->e_lead_id = $lead->id;
//        } else {
//            $clientEmail = ClientEmail::find()->where(['email' => $this->e_email_from])->orderBy(['id' => SORT_DESC])->limit(1)->one();
//            if($clientEmail && $clientEmail->client_id) {
//                $lead = Lead::find()->where(['client_id' => $clientEmail->client_id, 'status' => [Lead::STATUS_PROCESSING, Lead::STATUS_SNOOZE, Lead::STATUS_ON_HOLD, Lead::STATUS_FOLLOW_UP]])->orderBy(['id' => SORT_DESC])->limit(1)->one();
//                if(!$lead) {
//                    $lead = Lead::find()->where(['client_id' => $clientEmail->client_id])->orderBy(['id' => SORT_DESC])->limit(1)->one();
//                }
//                if($lead) {
//                    $this->e_lead_id = $lead->id;
//                }
//            }
//        }
//
//        return $this->e_lead_id;
    }


    /**
     * @deprecated
     * @return array
     */
    public function getUsersIdByEmail(): array
    {
        $users = [];
//        $params = UserProjectParams::find()->where(['upp_email' => $this->e_email_to])->all();
        $params = UserProjectParams::find()->byEmail($this->e_email_to, false)->all();

        if ($params) {
            foreach ($params as $param) {
                $users[$param->upp_user_id] = $param->upp_user_id;
            }
        }

        $employees = Employee::find()->where(['email' => $this->e_email_to])->all();

        if ($employees) {
            foreach ($employees as $employe) {
                $users[$employe->id] = $employe->id;
            }
        }

        return $users;
    }

    /**
     * @deprecated
     */
    public function getUserIdByEmail(string $email): int
    {
        $user = [];
        $params = UserProjectParams::find()->byEmail($email, false)->all();

        if ($params) {
            foreach ($params as $param) {
                $user[$param->upp_user_id] = $param->upp_user_id;
            }
        }

        $employees = Employee::find()->where(['email' => $email])->all();

        if ($employees) {
            foreach ($employees as $employee) {
                $users[$employee->id] = $employee->id;
            }
        }

        return reset($user);
    }


    /*
    public static function reSubject($str = '') : string
    {
        $str = trim($str);
        if(strpos($str, 'Re:', 0) === false && strpos($str, 'Re[', 0) === false) {
            return 'Re:'. $str;
        } else {
            if(mb_substr($str, 0,3, 'utf-8') === 'Re:') {
                return preg_replace("/(Re:)/i", 'Re[1]:', $str, 1);
            } elseif(preg_match('/Re\[([\d]+)\]:/i', $str, $matches)) {
                if(isset($matches[0], $matches[1])) {
                    $newVal = $matches[1] + 1;
                    return preg_replace('/Re\[([\d]+)\]:/i', 'Re['.$newVal.']:', $str, 1);
                }
            }
        }
        return $str;
    }
    */

    /**
     * @param string $str
     * @return string
     */
    public static function reSubject($str = ''): string
    {
        $str = trim($str);
        if (strpos($str, 'Re:', 0) === false && strpos($str, 'Re[', 0) === false) {
            return 'Re:' . $str;
        } else {
            preg_match_all('/Re\[([\d]+)\]:/i', $str, $m);
            if ($m && is_array($m) && isset($m[0], $m[1])) {
                if (count($m[0]) > 1) {
                    $cnt = 0;
                    foreach ($m[0] as $repl) {
                        if (isset($m[0][$cnt + 1])) {
                            $from = '/' . preg_quote($repl, '/') . '/';
                            $str = preg_replace($from, '', $str, 1);
                            $str = preg_replace("/(.*?)$repl/i", '', $str, 1);
                        }
                        $cnt++;
                    }
                }
            }
            $str = preg_replace("/(.*?)Re\[([\d]+)\]:/i", 'Re[$2]: ', $str, 1);
            if (mb_substr($str, 0, 3, 'utf-8') === 'Re:') {
                $str = preg_replace("/(Re:)/i", 'Re[1]:', $str, 1);
            } elseif (preg_match('/Re\[([\d]+)\]:/i', $str, $matches)) {
                if (isset($matches[0], $matches[1])) {
                    $newVal = $matches[1] + 1;
                    $str = preg_replace('/Re\[([\d]+)\]:/i', 'Re[' . $newVal . ']:', $str, 1);
                }
            }
        }
        $str = preg_replace("/ {2,}/", " ", $str);

        return trim($str);
    }

    /**
     * @deprecated
     * @param string $startDate
     * @param string $endDate
     * @param string|null $groupingBy
     * @param int $emailsType
     * @return array
     * @throws \Exception
     */
    public static function getEmailsStats(string $startDate, string $endDate, ?string $groupingBy, int $emailsType): array
    {
        $sDate = $startDate . " 00:00:00";
        $eDate = $endDate . " 23:59:59";
        switch ($groupingBy) {
            case null:
                if (strtotime($startDate) == strtotime($endDate)) {
                    $hoursRange = ChartTools::getHoursRange($startDate, $endDate . " 23:59:59", $step = '+1 hour', $format = 'H:i:s');
                } else {
                    $daysRange = ChartTools::getDaysRange($startDate, $endDate);
                }
                break;
            case 'hours':
                if (strtotime($startDate) == strtotime($endDate)) {
                    $hoursRange = ChartTools::getHoursRange($startDate, $endDate . " 23:59:59", $step = '+1 hour', $format = 'H:i:s');
                } else {
                    $hoursRange = ChartTools::getHoursRange($startDate, $endDate . " 23:59:59", $step = '+1 hour', $format = 'Y-m-d H:i:s');
                }
                break;
            case 'days':
                $daysRange = ChartTools::getDaysRange($startDate, $endDate);
                break;
            case 'weeks':
                $weeksPeriods = ChartTools::getWeeksRange(new DateTime($startDate), new DateTime($endDate . ' 23:59'));
                break;
            case 'months':
                $monthsRange = ChartTools::getMonthsRange($startDate, $endDate);
                $sDate = date("Y-m-01", strtotime($startDate));
                $eDate = date('Y-m-31', strtotime($endDate));
                break;
        }
        if ($emailsType == 0) {
            $emails = self::find()->select(['e_status_id', 'e_created_dt'])
                ->where(['e_status_id' => [self::STATUS_DONE, self::STATUS_ERROR]])
                ->andWhere(['between', 'e_created_dt', $sDate, $eDate])
                ->all();
        } else {
            $emails = self::find()->select(['e_status_id', 'e_created_dt'])
                ->where(['e_status_id' => [self::STATUS_DONE, self::STATUS_ERROR]])
                ->andWhere(['between', 'e_created_dt', $sDate, $eDate])
                ->andWhere(['=', 'e_type_id', $emailsType])
                ->all();
        }

        $emailStats = [];
        $item = [];
        if (strtotime($startDate) < strtotime($endDate)) {
            if (isset($daysRange)) {
                $timeLine = $daysRange;
                $item['timeLine'] = 'd M';
                $timeInSeconds = 0;
                $dateFormat = 'Y-m-d';
            } elseif (isset($monthsRange)) {
                $timeLine = $monthsRange;
                $timeInSeconds = 0;
                $dateFormat = 'Y-m';
                $item['timeLine'] = 'Y, M';
            } elseif (isset($weeksPeriods)) {
                $timeLine = $weeksPeriods;
                $item['timeLine'] = 'd M';
                $timeInSeconds = 0;
                $dateFormat = 'Y-m-d';
            } elseif (isset($hoursRange)) {
                $timeLine = $hoursRange;
                $item['timeLine'] = 'H:i';
                $dateFormat = 'Y-m-d H:i:s';
                $timeInSeconds = 3600;
            }
        } else {
            if (isset($daysRange)) {
                $timeLine = $daysRange;
                $item['timeLine'] = 'd M';
                $timeInSeconds = 0;
                $dateFormat = 'Y-m-d';
            } elseif (isset($hoursRange)) {
                $timeLine = $hoursRange;
                $item['timeLine'] = 'H:i';
                $dateFormat = 'H:i:s';
                $timeInSeconds = 3600;
            } elseif (isset($monthsRange)) {
                $timeLine = $monthsRange;
                $timeInSeconds = 0;
                $dateFormat = 'Y-m';
                $item['timeLine'] = 'Y, M';
            } elseif (isset($weeksPeriods)) {
                $timeLine = $weeksPeriods;
                $item['timeLine'] = 'd M';
                $timeInSeconds = 0;
                $dateFormat = 'Y-m-d';
            }
        }

        $done = $error = 0;
        foreach ($timeLine as $key => $timeSignature) {
            $weekInterval = explode('/', $timeSignature);
            if (count($weekInterval) != 2) {
                $EndPoint = date($dateFormat, strtotime($timeSignature) + $timeInSeconds);
                if ($EndPoint == '00:00:00') {
                    $EndPoint = '23:59:59';
                }
            } else {
                $EndPoint = date($dateFormat, strtotime($weekInterval[1]));
                $timeSignature = date($dateFormat, strtotime($weekInterval[0]));
            }
            foreach ($emails as $emailItem) {
                $smsUpdatedTime = date($dateFormat, strtotime($emailItem->e_created_dt));
                if ($smsUpdatedTime >= $timeSignature && $smsUpdatedTime <= $EndPoint) {
                    switch ($emailItem->e_status_id) {
                        case self::STATUS_DONE:
                            $done++;
                            break;
                        case self::STATUS_ERROR:
                            $error++;
                            break;
                    }
                }
            }
            $item['time'] = $timeSignature;
            $item['weeksInterval'] = (count($weekInterval) == 2) ? $EndPoint : null;
            $item['done'] = $done;
            $item['error'] = $error;

            array_push($emailStats, $item);
            $done = $error = 0;
        }
        return $emailStats;
    }

    /**
     * @deprecated
     */
    public static function getProjectIdByDepOrUpp($emailTo)
    {
        if ($dep = DepartmentEmailProject::find()->byEmail($emailTo)->one()) {
            return $dep->dep_project_id;
        } else if ($upp = UserProjectParams::find()->byEmail($emailTo)->one()) {
            return $upp->upp_project_id;
        }

        return null;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->e_lead_id && $this->lead) {
            $this->lead->updateLastAction(LeadPoorProcessingLogStatus::REASON_EMAIL);
        }
        if ($this->e_case_id && $this->case) {
            $this->case->updateLastAction();
        }
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if (!empty($this->body_html)) {
                $this->e_email_body_text = TextConvertingHelper::htmlToText($this->body_html);
                $this->e_email_body_blob = TextConvertingHelper::compress($this->body_html);
            }

            if (!$this->e_client_id) {
                $emailServiceHelper = Yii::createObject(EmailServiceHelper::class);
                $this->e_client_id = $emailServiceHelper->detectClientId($this->e_email_to);
            }

            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getEmailBodyHtml(): ?string
    {
        if (!empty($this->e_email_body_blob)) {
            $value = TextConvertingHelper::unCompress($this->e_email_body_blob);
        } else {
            $value = '';
        }
        return $value;
    }

    public function isCreatedUser(int $userId): bool
    {
        return $this->e_created_user_id === $userId;
    }

    public function hasCreatedUser(): bool
    {
        return $this->e_created_user_id ? true : false;
    }

    public function statusToReview()
    {
        $this->updateAttributes(['e_status_id' => self::STATUS_REVIEW]);

        return $this;
    }

    public function statusIsReview(): bool
    {
        return $this->e_status_id === self::STATUS_REVIEW;
    }

    public function statusIsErrorGroup(): bool
    {
        return $this->e_status_id === self::STATUS_ERROR || $this->e_status_id === self::STATUS_CANCEL;
    }

    public function statusIsDone(): bool
    {
        return $this->e_status_id === self::STATUS_DONE;
    }

    /**
     *
     * @param string $errorMessage
     * @return Email
     */
    public function statusToCancel(string $errorMessage)
    {
        $this->updateAttributes([
            'e_error_message' => $errorMessage,
            'e_status_id' => self::STATUS_CANCEL,
        ]);

        return $this;
    }

    /**
     *
     * @param string $errorMessage
     * @return Email
     */
    public function statusToError(string $errorMessage)
    {
        $this->updateAttributes([
            'e_error_message' => $errorMessage,
            'e_status_id' => self::STATUS_ERROR,
        ]);

        return $this;
    }

    public function getEmailFrom($masking = true): ?string
    {
        return (EmailType::isInbox($this->e_type_id) && $masking) ?  MaskEmailHelper::masking($this->e_email_from) : $this->e_email_from;
    }

    public function getEmailTo($masking = true): ?string
    {
        return (EmailType::isOutbox($this->e_type_id) && $masking) ?  MaskEmailHelper::masking($this->e_email_to) : $this->e_email_to;
    }

    public function getEmailFromName(): ?string
    {
        return $this->e_email_from_name;
    }

    public function getEmailToName(): ?string
    {
        return $this->e_email_to_name;
    }

    public function hasLead(): bool
    {
        return $this->e_lead_id !== null;
    }

    public function hasCase(): bool
    {
        return $this->e_case_id !== null;
    }

    public function hasClient(): bool
    {
        return $this->e_client_id !== null;
    }

    public function getProjectId(): ?int
    {
        return $this->e_project_id;
    }

    public function getDepartmentId(): ?int
    {
        return $this->lead->l_dep_id ?? null;
    }

    public function getTemplateTypeId(): ?int
    {
        return $this->e_template_type_id;
    }

    public function getLeadId(): ?int
    {
        return $this->e_lead_id;
    }

    public function getCaseId(): ?int
    {
        return $this->e_case_id;
    }

    public function getClientId(): ?int
    {
        return $this->e_client_id;
    }

    public function getLanguageId(): ?string
    {
        return $this->e_language_id;
    }

    public function getStatusDoneDt(): ?string
    {
        return $this->e_status_done_dt;
    }

    public function getErrorMessage(): ?string
    {
        return $this->e_error_message;
    }

    public function getCommunicationId()
    {
        return $this->e_communication_id;
    }

    public function getEmailSubject()
    {
        return $this->e_email_subject;
    }

    public function isDeleted(): bool
    {
        return $this->e_is_deleted;
    }

    public function isNew(): bool
    {
        return $this->e_is_new ?? false;
    }

    public function read(): void
    {
        if ($this->isNew()) {
            $this->updateAttributes([
                'e_is_new' => false,
                'e_read_dt' => date('Y-m-d H:i:s')
            ]);
        }
    }
    public function saveInboxId(int $inboxId): void
    {
        $this->updateAttributes([
            'e_inbox_email_id' => $inboxId
        ]);
    }
}
