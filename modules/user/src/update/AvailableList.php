<?php

namespace modules\user\src\update;

use common\models\Employee;
use modules\shiftSchedule\src\entities\shift\Shift;
use src\model\clientChatChannel\entity\ClientChatChannel;
use yii\helpers\ArrayHelper;

/**
 * Class AvailableList
 *
 * @property Employee $user
 */
class AvailableList
{
    private Employee $user;

    public function __construct(Employee $user)
    {
        $this->user = $user;
    }

    public function getStatuses(): array
    {
        return Employee::getStatusList();
    }

    public function getRoles(): array
    {
        return ArrayHelper::map(\Yii::$app->authManager->getRoles(), 'name', 'description');
    }

    public function getUserShiftAssign(): array
    {
        //todo validate available shifts for updater user
        return Shift::getList();
    }

    public function getClientChatUserChannels(): array
    {
        //todo validate available client chats for updater user
        return ClientChatChannel::getList();
    }

    public function getProjects(): array
    {
        return \common\models\Project::getList(true);
    }

    public function getDepartments(): array
    {
        //todo validate available departments for updater user
        return \common\models\Department::getList();
    }

    public function getUserGroups(): array
    {
        return \common\models\UserGroup::getList(true);
    }

    public function getTimezones(): array
    {
        return Employee::timezoneList(true);
    }

    public function getCallTypes(): array
    {
        return \common\models\UserProfile::CALL_TYPE_LIST;
    }

    public function getSkillTypes(): array
    {
        return \common\models\UserProfile::SKILL_TYPE_LIST;
    }
}
