<?php

namespace modules\qaTask\src\entities\qaTask\search;

use common\models\Department;
use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTaskCreateType;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\entities\qaTask\QaTaskRating;
use modules\qaTask\src\entities\qaTaskCategory\QaTaskCategoryQuery;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\helpers\formatters\QaTaskCategoryFormatter;
use sales\access\QueryAccessService;
use Webmozart\Assert\Assert;
use yii\base\BaseObject;

/**
 * Class CreateDto
 *
 * @property Employee|null $user
 * @property array|null $projectList
 * @property array|null $userList
 * @property array|null $objectTypeList
 * @property array|null $statusList
 * @property array|null $ratingList
 * @property array|null $createdTypeList
 * @property array|null $departmentList
 * @property array|null $categoryList
 * @property QueryAccessService|null $queryAccessService
 */
class CreateDto extends BaseObject
{
    public $user;
    public $projectList;
    public $userList;
    public $objectTypeList;
    public $statusList;
    public $ratingList;
    public $createdTypeList;
    public $departmentList;
    public $categoryList;
    public $queryAccessService;

    public function init(): void
    {
        if ($this->user === null) {
            throw new \InvalidArgumentException('user must be set.');
        }
        if (!$this->user instanceof Employee) {
            throw new \InvalidArgumentException('user must be instance of ' . Employee::class);
        }

        if ($this->projectList === null) {
            throw new \InvalidArgumentException('projectList must be set.');
        }
        Assert::isArray($this->projectList);

        if ($this->userList === null) {
            throw new \InvalidArgumentException('userList must be set.');
        }
        Assert::isArray($this->userList);

        if ($this->queryAccessService === null) {
            throw new \InvalidArgumentException('queryAccessService must be set.');
        }
        if (!$this->queryAccessService instanceof QueryAccessService) {
            throw new \InvalidArgumentException('queryAccessService must be instance of ' . QueryAccessService::class);
        }

        if ($this->objectTypeList === null) {
            $this->objectTypeList = QaTaskObjectType::getList();
        }
        Assert::isArray($this->objectTypeList);

        if ($this->statusList === null) {
            $this->statusList = QaTaskStatus::getList();
        }
        Assert::isArray($this->statusList);

        if ($this->ratingList === null) {
            $this->ratingList = QaTaskRating::getList();
        }
        Assert::isArray($this->ratingList);

        if ($this->createdTypeList === null) {
            $this->createdTypeList = QaTaskCreateType::getList();
        }
        Assert::isArray($this->createdTypeList);

        if ($this->departmentList === null) {
            $this->departmentList = Department::DEPARTMENT_LIST;
        }
        Assert::isArray($this->departmentList);

        if ($this->categoryList === null) {
            $this->categoryList = QaTaskCategoryFormatter::format(QaTaskCategoryQuery::getListEnabled());
        }
        Assert::isArray($this->categoryList);

        parent::init();
    }
}
