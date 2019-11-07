<?php

namespace frontend\widgets;

use common\models\Employee;
use yii\base\Widget;

/**
 * Class UserInfoProgress
 *
 * @property Employee $user
 */
class UserInfoProgress extends Widget
{
    public $user;

    public function init()
    {
        parent::init();
        if (!$this->user instanceof Employee) {
            throw new \InvalidArgumentException('user must be Employee');
        }
    }

    public function run(): string
    {
        return $this->render('user_info_progress', [
            'user' => $this->user,
            'completedTasksPercent' => $this->user->getCurrentShiftTaskInfoSummary()['completedTasksPercent'],
            'newLeadsCount' => $this->user->getCountNewLeadCurrentShift()
        ]);
    }
}
