<?php

namespace modules\taskList\src\entities\userTask;

/**
 * @see UserTaskStatusLog
 */
class UserTaskStatusLogScopes extends \yii\db\ActiveQuery
{
    /**
     * @return UserTaskStatusLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @return UserTaskStatusLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
