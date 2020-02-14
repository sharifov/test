<?php

namespace modules\qaTask\src\useCases\qaTask\takeOver;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTaskActionReason\QaTaskActionReasonQuery;
use modules\qaTask\src\entities\qaTaskActionReason\ReasonDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use yii\base\Model;

/**
 * Class QaTaskActionTakeOverForm
 *
 * @property int $reasonId
 * @property string|null $description
 * @property QaTask $task
 * @property Employee $user
 * @property ReasonDto[] $reasons
 */
class QaTaskTakeOverForm extends Model
{
    public $reasonId;
    public $description;

    private $task;
    private $user;
    private $reasons;

    public function __construct(QaTask $task, Employee $user, $config = [])
    {
        $this->task = $task;
        $this->user = $user;
        $this->reasons = QaTaskActionReasonQuery::getReasons($this->task->t_object_type_id, QaTaskActions::TAKE_OVER);
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
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

    public function getReasonList(): array
    {
        $list = [];
        foreach ($this->reasons as $reason) {
            $list[$reason->id] = $reason->name;
        }
        return $list;
    }

    public function attributeLabels(): array
    {
        return [
            'reasonId' => 'Reason',
            'description' => 'Description',
        ];
    }
}
