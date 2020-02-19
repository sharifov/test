<?php

namespace modules\qaTask\src\useCases\qaTask;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use yii\base\Model;

/**
 * Class QaTaskActionForm
 *
 * @property QaTask $task
 * @property Employee $user
 */
class QaTaskActionForm extends Model
{
    protected $task;
    protected $user;

    public function __construct(QaTask $task, Employee $user, $config = [])
    {
        $this->task = $task;
        $this->user = $user;
        parent::__construct($config);
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
}
