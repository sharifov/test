<?php

namespace frontend\widgets\multipleUpdate\lead;

use common\models\Employee;
use common\models\Lead;
use common\models\Reason;
use frontend\widgets\multipleUpdate\IdsValidator;
use sales\access\ListsAccess;
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
    public const REDIAL_ADD = 1;
    public const REDIAL_REMOVE = 2;

    public $ids;
    public $statusId;
    public $userId;
    public $reason;
    public $message;
    public $redial_queue;

    private $authUser;

    public function __construct(Employee $authUser, $config = [])
    {
        parent::__construct($config);
        $this->authUser = $authUser;
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
                return $this->reason === $this->reasonOther();
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
                $this->message = Reason::getReasonByStatus($this->statusId, $this->reason);
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

        return in_array($this->statusId, [
            Lead::STATUS_TRASH,
            Lead::STATUS_REJECT,
            Lead::STATUS_FOLLOW_UP
        ], true);
    }

    public function reasonList(): array
    {
        return Reason::STATUS_REASON_LIST;
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
}
