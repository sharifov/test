<?php

namespace modules\qaTask\src\entities\qaTask\search;

use common\models\Department;
use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskCreatedType;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\entities\qaTask\QaTaskRating;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;

/**
 * Class QaTaskSearch
 *
 * @property Employee $user
 * @property array $projectList
 * @property array $objectTypeList
 * @property array $statusList
 * @property array $ratingList
 * @property array $createdTypeList
 * @property array $departmentList
 */
class QaTaskSearch extends QaTask
{
    protected $user;

    private $projectList;
    private $objectTypeList;
    private $statusList;
    private $ratingList;
    private $createdTypeList;
    private $departmentList;

    public function __construct(Employee $user, array $projectList, $config = [])
    {
        $this->user = $user;
        $this->projectList = $projectList;
        $this->objectTypeList = QaTaskObjectType::getList();
        $this->statusList = QaTaskStatus::getList();
        $this->ratingList = QaTaskRating::getList();
        $this->createdTypeList = QaTaskCreatedType::getList();
        $this->departmentList = Department::DEPARTMENT_LIST;
        parent::__construct($config);
    }

    public function getProjectList(): array
    {
        return $this->projectList;
    }

    public function getObjectTypeList(): array
    {
        return $this->objectTypeList;
    }

    public function getStatusList(): array
    {
        return $this->statusList;
    }

    public function getRatingList(): array
    {
        return $this->ratingList;
    }

    public function getCreatedTypeList(): array
    {
        return $this->createdTypeList;
    }

    public function getDepartmentList(): array
    {
        return $this->departmentList;
    }
}
