<?php

namespace sales\model\department\departmentPhoneProject\useCases\api\get;

use common\models\Department;
use common\models\Project;
use yii\base\Model;

/**
 * Class DepartmentPhoneProjectForm
 *
 * @property int $project_id
 * @property string $department
 * @property int $department_id
 */
class DepartmentPhoneProjectForm extends Model
{
    public $project_id;
    public $department;
    public $department_id;

    public function rules(): array
    {
        return [
            ['project_id', 'required'],
            ['project_id', 'integer'],
            ['project_id', 'exist', 'targetClass' => Project::class, 'targetAttribute' => ['project_id' => 'id']],

            ['department', 'in', 'range' => Department::DEPARTMENT_LIST],
        ];
    }

    public function afterValidate()
    {
        parent::afterValidate();
        if ($this->department && !$this->getErrors()) {
            $list = array_flip(Department::DEPARTMENT_LIST);
            $this->department_id = $list[$this->department] ?? null;
        }
    }

    public function formName(): string
    {
        return '';
    }
}
