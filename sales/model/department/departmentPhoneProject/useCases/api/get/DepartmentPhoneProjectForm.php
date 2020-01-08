<?php

namespace sales\model\department\departmentPhoneProject\useCases\api\get;

use common\models\Department;
use common\models\Project;
use common\models\Sources;
use yii\base\Model;

/**
 * Class DepartmentPhoneProjectForm
 *
 * @property int $project_id
 * @property int $source_id
 * @property int $department_id
 */
class DepartmentPhoneProjectForm extends Model
{
    public $project_id;
    public $source_id;
    public $department_id;

    public function rules(): array
    {
        return [
            ['project_id', 'required'],
            ['project_id', 'integer'],
            ['project_id', 'exist', 'targetClass' => Project::class, 'targetAttribute' => ['project_id' => 'id']],

            ['source_id', 'integer'],
            ['source_id', 'exist', 'targetClass' => Sources::class, 'targetAttribute' => ['source_id' => 'id']],

            ['department_id', 'in', 'range' => array_keys(Department::DEPARTMENT_LIST)],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
