<?php

namespace sales\forms\cases;

use common\models\Employee;
use sales\access\ListsAccess;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use sales\entities\cases\CasesStatusTransferList;
use yii\base\Model;
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
 * @property Cases $case
 * @property Employee $user
 */
class CasesChangeStatusForm extends Model
{
    public $statusId;
    public $reason;
    public $message;
    public $userId;

    public $caseGid;

    private $case;
    private $user;

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
            ['userId', 'required', 'when' =>  function() {
                return $this->isProcessing();
            }, 'skipOnEmpty' => false],
        ];
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
        return (new ListsAccess($this->user->id))->getEmployees();
    }

    /**
     * @return array
     */
    public function statusList(): array
    {
        $list = CasesStatusTransferList::getAllowTransferListByUser($this->case->cs_status, $this->user);

        if (!$this->user->isAdmin()) {
            if (isset($list[CasesStatus::STATUS_PROCESSING])) {
                unset($list[CasesStatus::STATUS_PROCESSING]);
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
        ];
    }
}
