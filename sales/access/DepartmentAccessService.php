<?php

namespace sales\access;

use common\models\Department;
use common\models\Employee;
use yii\rbac\CheckAccessInterface;

/**
 * Class DepartmentAccessService
 *
 * @property CheckAccessInterface $accessChecker
 */
class DepartmentAccessService
{
    private const PERMISSION = 'access/department/all';

    private $accessChecker;

    public function __construct(CheckAccessInterface $accessChecker)
    {
        $this->accessChecker = $accessChecker;
    }

    public function checkAccess(Employee $user, int $departmentId): bool
    {
        if ($this->accessChecker->checkAccess($user->id, self::PERMISSION)) {
            return true;
        }

        return array_key_exists($departmentId, $user->getAccess()->getDepartments());
    }

    public function guard(Employee $user, int $departmentId): void
    {
        if (!$this->checkAccess($user, $departmentId)) {
            throw new \DomainException('User: ' . $user->username . ' cant access to department: ' . $departmentId . '.');
        }
    }

    public function getDepartments(Employee $user): array
    {
        if ($this->accessChecker->checkAccess($user->id, self::PERMISSION)) {
            return Department::find()->select(['dep_name'])->indexBy('dep_id')->column();
        }

        return $user->getAccess()->getDepartments();
    }

    public function processQuery(Employee $user, DepartmentQueryInterface $query): void
    {
        if ($this->accessChecker->checkAccess($user->id, self::PERMISSION)) {
            return;
        }

        $query->departments(array_keys($user->getAccess()->getDepartments()));
    }
}
