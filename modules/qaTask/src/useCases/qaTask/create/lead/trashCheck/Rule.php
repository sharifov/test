<?php

namespace modules\qaTask\src\useCases\qaTask\create\lead\trashCheck;

use Webmozart\Assert\Assert;

/**
 * Class Rule
 * @package modules\qaTask\src\useCases\qaTask\create\lead\checkTrash
 *
 * @property array $departments
 * @property array $projects
 * @property array $reasons
 * @property string $qaTaskCategoryKey
 * @property string $errorMessage
 */
class Rule
{
    public array $departments = [];

    public array $projects = [];

    public array $reasons = [];

    public string $qaTaskCategoryKey = '';

    public string $errorMessage = '';

    public function __construct(array $params)
    {
        try {
            Assert::keyExists($params, 'departments');
            Assert::keyExists($params, 'projects');
            Assert::keyExists($params, 'reasons');
            Assert::keyExists($params, 'qa_task_category_key');

            Assert::isArray($params['departments']);
            Assert::isArray($params['projects']);
            Assert::isArray($params['reasons']);

            if (!empty($params['reasons'])) {
                Assert::allNumeric($params['reasons'], 'QA Task Rule (lead_trash_check) has non numeric values in reason option');
            }

            Assert::notEmpty($params['qa_task_category_key'], 'QA Task Rule (lead_trash_check) has empty required field: qa_task_category_key');
        } catch (\Throwable $e) {
            throw new RuleException($e->getMessage());
        }

        $this->departments = $params['departments'];
        $this->projects = $params['projects'];
        $this->reasons = $params['reasons'];
        $this->qaTaskCategoryKey = $params['qa_task_category_key'];
    }

    public function guard(?string $leadDepKey, ?string $leadProjectKey, ?string $trashActionReason): bool
    {
        if ($leadDepKey && !$this->guardDepartment($leadDepKey)) {
            $this->errorMessage = 'Qa Task cannot be created for this lead department (' . $leadDepKey . ')';
            return false;
        }

        if ($leadProjectKey && !$this->guardProjects($leadProjectKey)) {
            $this->errorMessage = 'Qa Task cannot be created for this lead project (' . $leadProjectKey . ')';
            return false;
        }

        if ($trashActionReason && !$this->guardActionReason(trim($trashActionReason))) {
            $this->errorMessage = 'Qa Task cannot be created for this trash action reason (' . $trashActionReason . ')';
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

    private function guardActionReason(string $reason): bool
    {
        if (empty($this->reasons)) {
            return true;
        }

        if (array_key_exists($reason, $this->reasons)) {
            $chance = $this->reasons[$reason];
            if ($chance >= 100) {
                return true;
            }
            if ($chance < 1) {
                return false;
            }
            $random = random_int(1, 99);
            return $random <= $chance;
        }

        return false;
    }
}
