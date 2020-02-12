<?php

namespace modules\qaTask\src\useCases\qaTask\takeOver;

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
 * @property ReasonDto[] $reasons
 */
class QaTaskTakeOverForm extends Model
{
    public $reasonId;
    public $description;

    private $task;
    private $reasons;

    public function __construct(QaTask $task, $config = [])
    {
        $this->task = $task;
        $this->reasons = QaTaskActionReasonQuery::getReasons($this->task->t_object_type_id, QaTaskActions::TAKE_OVER);
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['reasonId', 'required'],
            ['reasonId', 'integer'],
            ['reasonId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['reasonId', 'in', 'range' => array_keys($this->reasons)],

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
}
