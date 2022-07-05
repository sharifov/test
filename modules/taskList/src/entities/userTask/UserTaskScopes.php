<?php

namespace modules\taskList\src\entities\userTask;

/**
* @see UserTask
*/
class UserTaskScopes extends \yii\db\ActiveQuery
{
    /**
    * @return UserTask[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return UserTask|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
