<?php

namespace sales\forms\cases;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\Department;
use common\models\Employee;
use common\models\Project;
use sales\entities\cases\CasesCategory;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class CasesCreateByWebForm
 *
 * @property int $projectId
 * @property string $subject
 * @property string $description
 * @property string $category
 * @property string $clientPhone
 * @property int $depId
 *
 * @property Employee $user
 */
class CasesCreateByWebForm extends Model
{

    public $projectId;
    public $subject;
    public $description;
    public $category;
    public $clientPhone;
    public $depId;

    private $user;
    private $cache_projects;
    private $cache_departments;

    /**
     * CasesCreateByWebForm constructor.
     * @param Employee $user
     * @param array $config
     */
    public function __construct(Employee $user, $config = [])
    {
        parent::__construct($config);
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['projectId', 'required'],
            ['projectId', 'in', 'range' => array_keys($this->getProjects())],

            ['subject', 'string', 'max' => 255],

            ['description', 'string'],

            ['depId', 'required'],
            ['depId', 'in', 'range' => array_keys($this->getDepartments())],

            ['category', 'required'],
            ['category', 'string', 'max' => 50],
            ['category', 'in', 'range' => function() {
                return $this->getAvailableCategories($this->depId);
            }],

            ['clientPhone', 'required'],
            ['clientPhone', 'string', 'max' => 100],
            ['clientPhone', PhoneInputValidator::class],
            ['clientPhone', 'filter', 'filter' => function($value) {
                return str_replace('-', '', trim($value));
            }],
        ];
    }

    /**
     * @return array
     */
    public function getProjects(): array
    {
        if ($this->cache_projects) {
            return $this->cache_projects;
        }

        if ($this->user->isAdmin()) {
            $this->cache_projects = Project::getList();
            return $this->cache_projects;
        }

        $this->cache_projects = Project::getListByUser($this->user->id);
        return $this->cache_projects;
    }

    /**
     * @return array
     */
    public function getDepartments(): array
    {
        if ($this->cache_departments) {
            return $this->cache_departments;
        }

        if ($this->user->isAdmin()) {
            $this->cache_departments = Department::getList();
            return $this->cache_departments;
        }

        $this->cache_departments = ArrayHelper::map($this->user->udDeps, 'dep_id', 'dep_name');
        return $this->cache_departments;
    }

    /**
     * @param $depId
     * @return array
     */
    private function getAvailableCategories($depId): array
    {
        $depId = (int)$depId;
        $categories = CasesCategory::find()->select(['cc_key'])->andWhere(['cc_dep_id' => $depId])->asArray()->all();
        return array_column($categories, 'cc_key');
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'projectId' => 'Project',
            'subject' => 'Subject',
            'description' => 'Description',
            'depId' => 'Department',
            'category' => 'Category',
            'clientPhone' => 'Phone',
        ];
    }

}