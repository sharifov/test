<?php

namespace modules\qaTask\src\useCases\qaTask\cancel;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTaskActionReason\QaTaskActionReasonQuery;
use modules\qaTask\src\entities\qaTaskActionReason\ReasonDto;
use modules\qaTask\src\useCases\qaTask\QaTaskActionForm;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;

/**
 * Class QaTaskCancelForm
 *
 * @property int $reasonId
 * @property string|null $description
 * @property ReasonDto[] $reasons
 */
class QaTaskCancelForm extends QaTaskActionForm
{
    public $reasonId;
    public $description;

    private $reasons;

    public function __construct(QaTask $task, Employee $user, $config = [])
    {
        parent::__construct($task, $user, $config);
        $this->reasons = QaTaskActionReasonQuery::getReasons($this->task->t_object_type_id, QaTaskActions::CANCEL);
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
