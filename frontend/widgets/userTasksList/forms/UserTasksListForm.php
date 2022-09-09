<?php

namespace frontend\widgets\userTasksList\forms;

use yii\base\Model;

/**
 * @property int $gid
 * @property int|null $userShiftScheduleId
 * @property int|null $page
 */
class UserTasksListForm extends Model
{
    public $gid;
    public $userShiftScheduleId;
    public $page;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['gid', 'string', 'max' => 32],
            ['page', 'integer'],
            ['userShiftScheduleId', 'integer'],
        ];
    }
}
