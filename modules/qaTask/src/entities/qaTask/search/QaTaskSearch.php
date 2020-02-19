<?php

namespace modules\qaTask\src\entities\qaTask\search;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskSearch
 *
 * @property Employee $user
 * @property array $projectList
 * @property array $userList
 * @property array $objectTypeList
 * @property array $statusList
 * @property array $ratingList
 * @property array $createdTypeList
 * @property array $departmentList
 * @property array $categoryList
 */
class QaTaskSearch extends QaTask
{
    protected $user;

    private $projectList = [];
    private $userList = [];
    private $objectTypeList = [];
    private $statusList = [];
    private $ratingList = [];
    private $createdTypeList = [];
    private $departmentList = [];
    private $categoryList = [];

    public static function createSearch(CreateDto $dto): self
    {
        $search = new static();
        $search->user = $dto->user;
        $search->projectList = $dto->projectList;
        $search->userList = $dto->userList;
        $search->objectTypeList = $dto->objectTypeList;
        $search->statusList = $dto->statusList;
        $search->ratingList = $dto->ratingList;
        $search->createdTypeList = $dto->createdTypeList;
        $search->departmentList = $dto->departmentList;
        $search->categoryList = $dto->categoryList;
        return $search;
    }

    public function getProjectList(): array
    {
        return $this->projectList;
    }

    public function getUserList(): array
    {
        return $this->userList;
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

    public function getCategoryList(): array
    {
        return $this->categoryList;
    }
}
