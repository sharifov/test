<?php

namespace sales\forms\cases;

use common\models\CaseSale;
use common\models\ClientEmail;
use common\models\ClientProject;
use common\models\Email;
use common\models\EmailTemplateType;
use common\models\EmailUnsubscribe;
use common\models\Employee;
use sales\access\ListsAccess;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use sales\entities\cases\CasesStatusTransferList;
use sales\helpers\user\UserDateTimeHelper;
use yii\base\Model;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * Class CasesChangeStatusForm
 *
 * @property int $statusId
 * @property string $reason
 * @property string $message
 * @property int $userId
 * @property array $statusList
 * @property string $caseGid
 * @property string|null $deadline
 * @property bool $resendFeedbackForm
 * @property string $sendTo
 * @property string $language
 * @property Cases $case
 * @property Employee $user
 * @property array|null $sendToList
 * @property array|null $languageList
 * @property bool|null $enabledSendFeedbackEmail
 */
class CasesChangeStatusForm extends Model
{
    public $statusId;
    public $reason;
    public $message;
    public $userId;
    public $deadline;
    public $resendFeedbackForm;
    public $sendTo;
    public $language;

    public $caseGid;

    private $case;
    private $user;
    private ?array $sendToList = null;
    private ?array $languageList = null;
    private ?bool $enabledSendFeedbackEmail = null;

    public function __construct(Cases $case, Employee $user, $config = [])
    {
        parent::__construct($config);
        $this->case = $case;
        $this->caseGid = $case->cs_gid;
        $this->user = $user;
    }

    public function rules(): array
    {
        return [
            ['statusId', 'required'],
            ['statusId', 'integer'],
            ['statusId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['statusId', 'in', 'range' => array_keys($this->statusList())],

            ['reason', 'string'],
            ['reason', 'required', 'when' => function () {
                return $this->isReasonable();
            }],
            ['reason', 'reasonValidate', 'when' => function () {
                return $this->isReasonable();
            }],

            ['message', 'string', 'max' => 255],
            ['message', 'required', 'when' => function () {
                return $this->isMessagable();
            }],

            ['userId', 'integer'],
            ['userId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['userId', 'required', 'when' =>  function () {
                return $this->isProcessing();
            }, 'skipOnEmpty' => false],

            ['deadline', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['deadline', function () {
                if (strtotime($this->deadline) < time()) {
                    $this->addError('deadline', 'Deadline should be later than now.');
                }
            }, 'skipOnError' => true, 'skipOnEmpty' => true],

            ['resendFeedbackForm', 'default', 'value' => false],
            ['resendFeedbackForm', 'boolean'],
            ['resendFeedbackForm', 'filter', 'filter' =>  function ($value) {
                if (!$this->isResendFeedbackEnable()) {
                    return true;
                }
                return $value;
            }, 'skipOnError' => true, 'skipOnEmpty' => true],

            ['sendTo', 'required', 'when' => function () {
                return $this->isSendFeedback();
            }, 'skipOnEmpty' => false],
            ['sendTo', 'in', 'range' => $this->getSendToList(), 'skipOnError' => true, 'skipOnEmpty' => true],

            ['language', 'required', 'when' => function () {
                return $this->isSendFeedback();
            }, 'skipOnEmpty' => false],
            ['language', 'in', 'range' => array_keys($this->getLanguageList()), 'skipOnEmpty' => true, 'skipOnError' => true],
        ];
    }

    public function getConvertedDeadline(): ?string
    {
        if ($this->deadline === null) {
            return null;
        }

        return (UserDateTimeHelper::convertUserTimeToUtc($this->deadline, $this->user->timezone))->format('Y-m-d H:i:s');
    }

    public function afterValidate()
    {
        parent::afterValidate();
        if ($this->reason && !$this->message && !$this->hasErrors()) {
            $this->message = $this->reason;
        }
    }

    public function userList(): array
    {
        if ($this->user->isAnyAgent()) {
            return Employee::getActiveUsersListFromCommonGroups($this->user->id);
        }
        return (new ListsAccess($this->user->id))->getEmployees();
    }

    /**
     * @return array
     */
    public function statusList(): array
    {
        $list = CasesStatusTransferList::getAllowTransferListByUser($this->case->cs_status, $this->user);

//        if (!$this->user->isAdmin() && !$this->user->isExSuper() && !$this->user->isSupSuper()) {
//            if (isset($list[CasesStatus::STATUS_PROCESSING])) {
//                unset($list[CasesStatus::STATUS_PROCESSING]);
//            }
//        }

        if ($this->case->isTrash() || $this->case->isFollowUp()) {
            if (isset($list[CasesStatus::STATUS_PENDING])) {
                unset($list[CasesStatus::STATUS_PENDING]);
            }
        }
        return $list;
    }

    public function reasonValidate(): void
    {
        if (!isset($this->getReasonList()[$this->statusId][$this->reason])) {
            $this->addError('reason', 'Unknown reason');
        }
    }

    public function isReasonable(): bool
    {
        if (!$this->statusId) {
            return false;
        }

        return array_key_exists($this->statusId, $this->getReasonList());
    }

    public function isMessagable(): bool
    {
        return $this->reason === $this->reasonOther();
    }

    public function reasons(): string
    {
        return Json::encode($this->getReasonList());
    }

    public function reasonOther(): string
    {
        return 'Other';
    }

    public function isProcessing(): bool
    {
        return $this->statusId === $this->statusProcessingId();
    }

    public function statusProcessingId(): int
    {
        return CasesStatus::STATUS_PROCESSING;
    }

    public function statusFollowUpId(): int
    {
        return CasesStatus::STATUS_FOLLOW_UP;
    }

    public function statusSolvedId(): int
    {
        return CasesStatus::STATUS_SOLVED;
    }

    public function isSolved(): bool
    {
        return $this->statusId === $this->statusSolvedId();
    }

    private function getReasonList(): array
    {
        return CasesStatus::STATUS_REASON_LIST;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'statusId' => 'Status',
            'reason' => 'Reason',
            'message' => 'Message',
            'userId' => 'Employee',
            'sendTo' => 'Send to',
            'language' => 'Language',
            'resendFeedbackForm' => 'Resend feedback form',
        ];
    }

    public function getSendToList(): array
    {
        if ($this->sendToList !== null) {
            return $this->sendToList;
        }

        $this->sendToList = [];

        $emails = ClientEmail::find()->select(['email'])
            ->andWhere(['client_id' => $this->case->cs_client_id])
            ->andWhere([
                'OR',
                ['IS', 'type', null],
                ['<>', 'type', ClientEmail::EMAIL_INVALID],
            ])
            ->orderBy(new Expression('FIELD (type, ' . ClientEmail::EMAIL_FAVORITE . ', ' . ClientEmail::EMAIL_VALID . ', ' . ClientEmail::EMAIL_NOT_SET . ', null)'))
            ->column();

        foreach ($emails as $email) {
            if (!EmailUnsubscribe::find()->andWhere(['eu_email' => $email, 'eu_project_id' => $this->case->cs_project_id])->exists()) {
                $this->sendToList[$email] = $email;
            }
        }

        return $this->sendToList;
    }

    public function getLanguageList(): array
    {
        if ($this->languageList !== null) {
            return $this->languageList;
        }
        $this->languageList = \common\models\Language::getLanguages(true);
        return $this->languageList;
    }

    public function isEnabledSendFeedbackEmail(): bool
    {
        if ($this->enabledSendFeedbackEmail !== null) {
            return $this->enabledSendFeedbackEmail;
        }

        if (!$this->case->cs_order_uid) {
            $this->enabledSendFeedbackEmail = false;
            return $this->enabledSendFeedbackEmail;
        }

        $clientIsUnsubscribe = ClientProject::find()
            ->andWhere(['cp_client_id' => $this->case->cs_client_id])
            ->andWhere(['cp_project_id' => $this->case->cs_project_id])
            ->andWhere(['cp_unsubscribe' => true])
            ->exists();

        if ($clientIsUnsubscribe) {
            $this->enabledSendFeedbackEmail = false;
            return $this->enabledSendFeedbackEmail;
        }

        if (!$this->getSendToList()) {
            $this->enabledSendFeedbackEmail = false;
            return $this->enabledSendFeedbackEmail;
        }

        if (!$this->case->cs_dep_id) {
            $this->enabledSendFeedbackEmail = false;
            return $this->enabledSendFeedbackEmail;
        }
        if (!$params = $this->case->department->getParams()) {
            $this->enabledSendFeedbackEmail = false;
            return $this->enabledSendFeedbackEmail;
        }
        if (!$params->object->type->isCase()) {
            $this->enabledSendFeedbackEmail = false;
            return $this->enabledSendFeedbackEmail;
        }
        $this->enabledSendFeedbackEmail = $params->object->case->isActiveFeedback();
        return $this->enabledSendFeedbackEmail;
    }

    public function isResendFeedbackEnable(): bool
    {
        $templateTypeId = EmailTemplateType::find()
            ->select(['etp_id'])
            ->andWhere(['etp_key' => $this->case->department->getParams()->object->case->feedbackTemplateTypeKey])
            ->asArray()
            ->one();

        if (!$templateTypeId) {
            return false;
        }

        return Email::find()
            ->andWhere([
                'e_case_id' => $this->case->cs_id,
                'e_status_id' => Email::STATUS_DONE,
                'e_template_type_id' => $templateTypeId['etp_id'],
            ])
            ->exists();
    }

    public function isSendFeedback(): bool
    {
        return $this->isSolved() && $this->isEnabledSendFeedbackEmail() && $this->resendFeedbackForm;
    }
}
