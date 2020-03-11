<?php

namespace frontend\widgets\multipleUpdate\cases;

use common\models\Employee;
use frontend\widgets\multipleUpdate\IdsValidator;
use sales\entities\cases\CasesStatus;
use sales\helpers\user\UserDateTimeHelper;
use yii\base\Model;
use yii\helpers\Json;

/**
 * Class Form
 *
 * @property int[] $ids
 * @property int|null $userId
 * @property int|null $statusId
 * @property $reason
 * @property string $message
 * @property string|null $deadline
 *
 * @property Employee $user
 */
class MultipleUpdateForm extends Model
{
    public $ids;
    public $statusId;
    public $userId;
    public $reason;
    public $message;
    public $deadline;

    private $user;

    public function __construct(Employee $user, $config = [])
    {
        parent::__construct($config);
        $this->user = $user;
    }

    public function rules(): array
    {
        return [
            ['ids', IdsValidator::class, 'skipOnEmpty' => false],

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
                return $this->reason === $this->reasonOther();
            }],

            ['userId', 'integer'],
            ['userId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['userId', 'required', 'when' =>  function() {
                return $this->isProcessing();
            }, 'skipOnEmpty' => false],

            ['deadline', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['deadline', function () {
                if (strtotime($this->deadline) < time()) {
                    $this->addError('deadline', 'Deadline should be later than now.');
                }
            }, 'skipOnError' => true, 'skipOnEmpty' => true],
        ];
    }

    public function getCreatorId(): int
    {
        return $this->user->id;
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
        return Employee::getActiveUsersList();
    }

    public function statusList(): array
    {
        $statusList = CasesStatus::STATUS_LIST;
        if (isset($statusList[CasesStatus::STATUS_PENDING])) {
            unset($statusList[CasesStatus::STATUS_PENDING]);
        }
        return $statusList;
    }

    public function reasonValidate(): void
    {
        if (!isset(CasesStatus::STATUS_REASON_LIST[$this->statusId][$this->reason])) {
            $this->addError('reason', 'Unknown reason');
        }
    }

    public function isReasonable(): bool
    {
        if (!$this->statusId) {
            return false;
        }

        return array_key_exists($this->statusId, CasesStatus::STATUS_REASON_LIST);
    }

    public function reasonList(): string
    {
        return Json::encode(CasesStatus::STATUS_REASON_LIST);
    }

    public function reasonOther(): string
    {
        return 'Other';
    }

    public function isChangeStatus(): bool
    {
        return $this->statusId && !$this->userId;
    }

    public function isPending(): bool
    {
        return $this->statusId === CasesStatus::STATUS_PENDING;
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

    public function isFollowUp(): bool
    {
        return $this->statusId === CasesStatus::STATUS_FOLLOW_UP;
    }

    public function isSolved(): bool
    {
        return $this->statusId === CasesStatus::STATUS_SOLVED;
    }

    public function isTrash(): bool
    {
        return $this->statusId === CasesStatus::STATUS_TRASH;
    }

    public function attributeLabels(): array
    {
        return [
            'ids' => 'Ids',
            'statusId' => 'Status',
            'userId' => 'Employee',
        ];
    }
}
