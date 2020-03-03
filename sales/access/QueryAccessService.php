<?php

namespace sales\access;

use common\models\Employee;
use yii\db\ActiveQuery;

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
    )
    {
        $this->projectAccessService = $projectAccessService;
        $this->departmentAccessService = $departmentAccessService;
    }

    public function processProject(Employee $user, ProjectQueryInterface $query): void
    {
        $this->projectAccessService->processQuery($user, $query);
    }

    public function processDepartments(Employee $user, ActiveQuery $query, string $fieldName): void
    {
        $this->departmentAccessService->processQuery($user, $query, $fieldName);
    }
}
