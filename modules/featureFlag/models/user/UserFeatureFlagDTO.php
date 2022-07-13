<?php

namespace modules\featureFlag\models\user;

use common\models\Employee;

/**
 * @property int|null $id
 * @property string|null $username
 * @property array|null $projects
 * @property array|null $groups
 * @property array|null $roles
 * @property array|null $departments
 */
class UserFeatureFlagDTO
{
    public ?int $id;
    public ?string $username;
    public ?array $projects;
    public ?array $groups;
    public ?array $roles;
    public ?array $departments;

    public function __construct(?Employee $user)
    {
        if ($user) {
            $this->id = $user->id;
            $this->username = $user->username;
            $this->roles = $user->getRoles(true);
            $this->projects = $user->access->getAllProjects('key');
            $this->groups = $user->access->getAllGroups();
            $this->departments = $user->access->getAllDepartments();
        }
    }
}
