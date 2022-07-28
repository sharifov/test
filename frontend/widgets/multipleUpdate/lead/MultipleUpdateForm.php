<?php

namespace frontend\widgets\multipleUpdate\lead;

use common\models\Employee;
use common\models\Lead;
use frontend\widgets\multipleUpdate\IdsValidator;
use src\access\ListsAccess;
use src\model\leadStatusReason\entity\LeadStatusReasonQuery;
use yii\base\Model;

/**
 * Class MultipleUpdateForm
 *
 * @property int[] $ids
 * @property int|null $userId
 * @property int|null $statusId
 * @property int $reason
 * @property string $message
 * @property int $redial_queue
 *
 * @property Employee $authUser
 */
class MultipleUpdateForm extends Model
{
    public const STATUS_REASON_LIST = [
        Lead::STATUS_TRASH => [
            1 => 'Purchased elsewhere',
            2 => 'Duplicate',
            3 => 'Travel dates passed',
            4 => 'Invalid phone number',
            5 => 'Canceled trip',
            6 => 'Test',
            7 => 'Transfer to Customer Care',
            8 => 'Transfer to Exchange',
            9 => 'Transfer to Schedule Change',
            0 => 'Other'
        ],
        Lead::STATUS_REJECT => [
            1 => 'Purchased elsewhere',
            2 => 'Flight date > 10 months',
            3 => 'Not interested',
            4 => 'Duplicate',
            5 => 'Too late',
            6 => 'Test',
            0 => 'Other'
        ],
        Lead::STATUS_FOLLOW_UP => [
            1 => 'Proper Follow Up Done',
            2 => "Didn't get in touch",
            0 => 'Other'
        ],
        Lead::STATUS_PROCESSING => [
            1 => 'N/A',
            2 => 'No Available',
            3 => 'Voice Mail Send',
            4 => 'Will call back',
            5 => 'Waiting the option',
            0 => 'Other'
        ],
        Lead::STATUS_ON_HOLD => [
            0 => 'Other'
        ],
        Lead::STATUS_SNOOZE => [
            1 => 'Travelling dates > 12 months',
            2 => 'Not ready to buy now',
            0 => 'Other'
        ],
    ];

    public const REDIAL_ADD = 1;
    public const REDIAL_REMOVE = 2;

    public $ids;
    public $statusId;
    public $userId;
    public $reason;
    public $message;
    public $redial_queue;

    private $authUser;

    public function __construct(Employee $authTipsUser, $config = [])
    {
        parent::__construct($config);
        $this->authUser = $authTipsUser;
    }

    public function rules(): array
    {
        return [
            ['ids', IdsValidator::class, 'skipOnEmpty' => false],

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
                return $this->reason === $this->reasonOther() || $this->isClosedAndCommentRequired();
            }],

            ['userId', 'integer'],
            ['userId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['userId', 'default', 'value' => null],

            ['redial_queue', 'integer'],
            ['redial_queue', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['redial_queue', 'in', 'range' => array_keys($this->getRedialQueueList())],
        ];
    }

    public function afterValidate()
    {
        parent::afterValidate();

        if ($this->isEmptyForm()) {
            $this->addError('ids', 'Not selected actions');
        }

        if (!$this->hasErrors()) {
            if ($this->reason && !$this->message) {
                $this->message = $this->getReasonByStatus($this->statusId, $this->reason);
            }
        }
    }

    public function needStatusUpdate(): bool
    {
        return $this->statusId ? true : false;
    }

    public function needOwnerUpdate(): bool
    {
        return $this->userId ? true : false;
    }

    private function isEmptyForm(): bool
    {
        return !$this->statusId && !$this->userId && !$this->redial_queue;
    }

    public function getRedialQueueList(): array
    {
        return [
            self::REDIAL_ADD => 'Add to Redial Queue',
            self::REDIAL_REMOVE => 'Remove from Redial Queue',
        ];
    }

    public function userList(): array
    {
        $employees = (new ListsAccess($this->authUser->id))->getEmployees(true);
        $employees[-1] = '--- REMOVE EMPLOYEE ---';
        return $employees;
    }

    public function statusList(): array
    {
        $statusList = Lead::getStatusList($this->authUser);
        if (isset($statusList[Lead::STATUS_ALTERNATIVE])) {
            unset($statusList[Lead::STATUS_ALTERNATIVE]);
        }
        if (isset($statusList[Lead::STATUS_BOOK_FAILED])) {
            unset($statusList[Lead::STATUS_BOOK_FAILED]);
        }
        return $statusList;
    }

    public function reasonValidate(): void
    {
        if (!isset($this->reasonList()[$this->statusId][$this->reason])) {
            $this->addError('reason', 'Unknown reason');
        }
    }

    public function isReasonable(): bool
    {
        if (!$this->statusId) {
            return false;
        }

        return false;
//        return in_array($this->statusId, [
//            Lead::STATUS_TRASH,
//            Lead::STATUS_REJECT,
//            Lead::STATUS_FOLLOW_UP
//        ], true);
    }

    public function reasonList(): array
    {
        $reasonList = self::STATUS_REASON_LIST;
        $reasonList[Lead::STATUS_CLOSED] = LeadStatusReasonQuery::getList();
        return $reasonList;
    }

    public function getReasonByStatus($status_id = 0, $reason_id = 0): string
    {
        return $this->reasonList()[$status_id][$reason_id] ?? '-';
    }

    public function reasonOther(): string
    {
        return '0';
    }

    public function isRedialProcess(): bool
    {
        return array_key_exists($this->redial_queue, $this->getRedialQueueList());
    }

    public function isRedialAdd(): bool
    {
        return $this->redial_queue === self::REDIAL_ADD;
    }

    public function isRedialRemove(): bool
    {
        return $this->redial_queue === self::REDIAL_REMOVE;
    }

    public function authUserIsAdmin(): bool
    {
        return $this->authUser->isAdmin();
    }

    public function authUserIsSupervisor(): bool
    {
        return $this->authUser->isSupervision();
    }

    public function authUserId(): int
    {
        return $this->authUser->id;
    }

    public function attributeLabels(): array
    {
        return [
            'ids' => 'Ids',
            'statusId' => 'Status',
            'userId' => 'Employee',
        ];
    }

    public function isPending(): bool
    {
        return $this->statusId === Lead::STATUS_PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->statusId === Lead::STATUS_PROCESSING;
    }

    public function isReject(): bool
    {
        return $this->statusId === Lead::STATUS_REJECT;
    }

    public function isFollowUp(): bool
    {
        return $this->statusId === Lead::STATUS_FOLLOW_UP;
    }

    public function isSold(): bool
    {
        return $this->statusId === Lead::STATUS_SOLD;
    }

    public function isTrash(): bool
    {
        return $this->statusId === Lead::STATUS_TRASH;
    }

    public function isBooked(): bool
    {
        return $this->statusId === Lead::STATUS_BOOKED;
    }

    public function isSnooze(): bool
    {
        return $this->statusId === Lead::STATUS_SNOOZE;
    }

    public function isNew(): bool
    {
        return $this->statusId === Lead::STATUS_NEW;
    }

    public function isExtraQueue(): bool
    {
        return $this->statusId === Lead::STATUS_EXTRA_QUEUE;
    }

    public function isBusinessExtraQueue(): bool
    {
        return $this->statusId === Lead::STATUS_BUSINESS_EXTRA_QUEUE;
    }

    public function isClosed(): bool
    {
        return $this->statusId === Lead::STATUS_CLOSED;
    }

    public function getClosedStatusId(): int
    {
        return Lead::STATUS_CLOSED;
    }

    public function isClosedAndCommentRequired(): bool
    {
        $reason = LeadStatusReasonQuery::getLeadStatusReasonByKey((string)$this->reason);
        return $this->isClosed() && $reason && $reason->lsr_comment_required;
    }
}
