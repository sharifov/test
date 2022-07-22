<?php

namespace modules\taskList\src\entities\userTask\behaviors;

use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskStatusLog;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class UserTaskStatusLogDeleteBehavior extends Behavior
{
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteStatusLogs',
        ];
    }

    public function deleteStatusLogs(): void
    {
        /** @var UserTask $this->owner */
        UserTaskStatusLog::deleteAll([
            'utsl_ut_id' => $this->owner->ut_id
        ]);
    }
}
