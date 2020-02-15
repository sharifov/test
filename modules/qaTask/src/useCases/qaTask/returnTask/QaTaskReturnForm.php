<?php

namespace modules\qaTask\src\useCases\qaTask\returnTask;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTaskActionReason\QaTaskActionReasonQuery;
use modules\qaTask\src\entities\qaTaskActionReason\ReasonDto;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use yii\base\Model;

/**
 * Class QaTaskCancelForm
 *
 * @property int $statusId
 * @property int $reasonId
 * @property string|null $description
 * @property QaTask $task
 * @property Employee $user
 * @property ReasonDto[] $reasons
 * @property array $statusList
 */
class QaTaskReturnForm extends Model
{
    public $statusId;
    public $reasonId;
    public $description;

    private $task;
    private $user;
    private $reasons;
    private $statusList;

    public function __construct(QaTask $task, Employee $user, bool $canToEscalate, $config = [])
    {
        $this->task = $task;
        $this->user = $user;
        $this->reasons = QaTaskActionReasonQuery::getReasons($this->task->t_object_type_id, QaTaskActions::RETURN);

        $this->statusList = [
            QaTaskStatus::PENDING => QaTaskStatus::getName(QaTaskStatus::PENDING)
        ];

        if ($canToEscalate) {
            $this->statusList[QaTaskStatus::ESCALATED] = QaTaskStatus::getName(QaTaskStatus::ESCALATED);
        }

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['statusId', 'required'],
            ['statusId', 'integer'],
            ['statusId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['statusId', 'in', 'range' => array_keys($this->getStatusList())],

            ['reasonId', 'required'],
            ['reasonId', 'integer'],
            ['reasonId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['reasonId', 'in', 'range' => array_keys($this->getReasonList())],

            ['description', 'string', 'max' => 255],
            ['description', 'required', 'when' => function () {
                return (isset($this->reasons[$this->reasonId]) && $this->reasons[$this->reasonId]->isCommentRequired());
            }, 'skipOnError' => true],
        ];
    }

    public function toPending(): bool
    {
        return $this->statusId === QaTaskStatus::PENDING;
    }

    public function toEscalate(): bool
    {
        return $this->statusId === QaTaskStatus::ESCALATED;
    }

    public function getStatusList(): array
    {
        return $this->statusList;
    }

    public function getReasonList(): array
    {
        $list = [];
        foreach ($this->reasons as $reason) {
            $list[$reason->id] = $reason->name;
        }
        return $list;
    }

    public function getTaskId(): int
    {
        return $this->task->t_id;
    }

    public function getTaskGid(): string
    {
        return $this->task->t_gid;
    }

    public function getUserId(): int
    {
        return $this->user->id;
    }

    public function attributeLabels(): array
    {
        return [
            'statusId' => 'Status',
            'reasonId' => 'Reason',
            'description' => 'Description',
        ];
    }
}
