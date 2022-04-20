<?php

namespace modules\cases\src\abac\update;

use src\access\EmployeeGroupAccess;
use src\entities\cases\Cases;

class UpdateAbacDto extends \stdClass
{
    public string $department_name;
    public ?int $category_id;
    public string $project_name;

    public function __construct(Cases $case)
    {
        $this->department_name = $case->department->dep_name ?? '';
        $this->category_id = $case->cs_category_id ?: null;
        $this->project_name = $case->project->name ?? '';
        $this->source_type_id = $case->cs_source_type_id;
    }
}
