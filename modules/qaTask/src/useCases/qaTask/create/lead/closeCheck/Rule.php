<?php

namespace modules\qaTask\src\useCases\qaTask\create\lead\closeCheck;

use Webmozart\Assert\Assert;

/**
 * Class Rule
 * @package modules\qaTask\src\useCases\qaTask\create\lead\closeCheck
 *
 * @property array $departments
 * @property array $projects
 * @property string $qaTaskCategoryKey
 * @property string $errorMessage
 */
class Rule
{
    public array $departments = [];

    public array $projects = [];

    public string $qaTaskCategoryKey = '';

    public string $errorMessage = '';

    public function __construct(array $params)
    {
        try {
            Assert::keyExists($params, 'departments');
            Assert::keyExists($params, 'projects');
            Assert::keyExists($params, 'qa_task_category_key');

            Assert::isArray($params['departments']);
            Assert::isArray($params['projects']);

            Assert::notEmpty($params['qa_task_category_key'], 'QA Task Rule (lead_close_check) has empty required field: qa_task_category_key');
        } catch (\Throwable $e) {
            throw new RuleException($e->getMessage());
        }

        $this->departments = $params['departments'];
        $this->projects = $params['projects'];
        $this->qaTaskCategoryKey = $params['qa_task_category_key'];
    }

    public function guard(?string $leadDepKey, ?string $leadProjectKey): bool
    {
        if ($leadDepKey && !$this->guardDepartment($leadDepKey)) {
            $this->errorMessage = 'Qa Task cannot be created for this lead department (' . $leadDepKey . ')';
            return false;
        }

        if ($leadProjectKey && !$this->guardProjects($leadProjectKey)) {
            $this->errorMessage = 'Qa Task cannot be created for this lead project (' . $leadProjectKey . ')';
            return false;
        }

        return true;
    }

    private function guardDepartment(string $depKey): bool
    {
        return !(!empty($this->departments) && !in_array($depKey, $this->departments, true));
    }

    private function guardProjects(string $projectKey): bool
    {
        return !(!empty($this->projects) && !in_array($projectKey, $this->projects, true));
    }
}
