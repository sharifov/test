<?php

namespace src\access;

use common\models\Employee;
use src\model\user\entity\Access;

/**
 * Class QueryAccessService
 *
 * @property ProjectAccessService $projectAccessService
 * @property DepartmentAccessService $departmentAccessService
 */
class QueryAccessService
{
    private $projectAccessService;
    private $departmentAccessService;

    public function __construct(
        ProjectAccessService $projectAccessService,
        DepartmentAccessService $departmentAccessService
    ) {
        $this->projectAccessService = $projectAccessService;
        $this->departmentAccessService = $departmentAccessService;
    }

    public function processProject(Access $userAccess, ProjectQueryInterface $query): void
    {
        $this->projectAccessService->processQuery($userAccess, $query);
    }

    public function processDepartments(Employee $user, DepartmentQueryInterface $query): void
    {
        $this->departmentAccessService->processQuery($user, $query);
    }
}
