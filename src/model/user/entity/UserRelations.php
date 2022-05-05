<?php

namespace src\model\user\entity;

use common\models\Employee;
use yii\helpers\ArrayHelper;

/**
 * Class UserRelations
 *
 * @property array $roles
 * @property array $groups
 * @property array $projects
 * @property array $departments
 * @property array $clientChatChannels
 * @property array $shiftAssigns
 */
class UserRelations
{
    private array $roles;
    private array $groups;
    private array $projects;
    private array $departments;
    private array $clientChatChannels;
    private array $shiftAssigns;

    public function __construct(Employee $user)
    {
        $this->roles = ArrayHelper::map(\Yii::$app->authManager->getRolesByUser($user->id), 'name', 'name');
        $this->groups = ArrayHelper::map($user->userGroupAssigns, 'ugs_group_id', 'ugs_group_id');
        $this->projects = ArrayHelper::map($user->projects, 'id', 'id');
        $this->departments = ArrayHelper::map($user->userDepartments, 'ud_dep_id', 'ud_dep_id');
        $this->clientChatChannels = ArrayHelper::map($user->clientChatUserChannel, 'ccuc_channel_id', 'ccuc_channel_id');
        $this->shiftAssigns = ArrayHelper::map($user->userShiftAssigns, 'usa_sh_id', 'usa_sh_id');
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function getProjects(): array
    {
        return $this->projects;
    }

    public function getDepartments(): array
    {
        return $this->departments;
    }

    public function getClientChatChannels(): array
    {
        return $this->clientChatChannels;
    }

    public function getShiftAssigns(): array
    {
        return $this->shiftAssigns;
    }
}
