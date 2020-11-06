<?php

namespace sales\forms\cases;

use common\models\Department;
use common\models\Employee;
use common\models\Project;
use sales\entities\cases\CasesSourceType;
use sales\model\clientChat\entity\ClientChat;
use sales\repositories\cases\CaseCategoryRepository;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class CasesCreateByChatForm
 *
 * @property int $projectId
 * @property string $subject
 * @property string $description
 * @property int $categoryId
 * @property int $depId
 * @property int $sourceTypeId
 * @property string|null $orderUid
 *
 * @property Employee $user
 */
class CasesCreateByChatForm extends Model
{

    public $projectId;
    public $subject;
    public $description;
    public $categoryId;
    public $depId;
    public $sourceTypeId;
    public $orderUid;

    private $user;
    private $cache_projects;
    private $cache_departments;

    public function __construct(Employee $tipsUser, ClientChat $chat, $config = [])
    {
        parent::__construct($config);
        $this->depId = $chat->cchChannel->ccc_dep_id;
        $this->projectId = $chat->cch_project_id;
        $this->sourceTypeId = CasesSourceType::CHAT;
        $this->user = $tipsUser;
    }

    public function rules(): array
    {
        return [
            ['projectId', 'required'],
            ['projectId', 'in', 'range' => array_keys($this->getProjects())],

            ['subject', 'string', 'max' => 255],

            ['description', 'string'],

            ['depId', 'required'],
            ['depId', 'integer'],
            ['depId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['depId', 'in', 'range' => array_keys($this->getDepartments())],

            ['categoryId', 'required'],
            ['categoryId', 'integer', 'skipOnError' => true],
            ['categoryId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['categoryId', 'validateCategory', 'skipOnError' => true],

            ['sourceTypeId', 'required'],
            ['sourceTypeId', 'integer'],
            ['sourceTypeId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['sourceTypeId', 'compare', 'compareValue' => CasesSourceType::CHAT, 'operator' => '==', 'type' => 'number'],

            ['orderUid', 'default', 'value' => null],
            ['orderUid', 'string', 'min'  => '5', 'max' => 7],
            ['orderUid', 'match', 'pattern' => '/^[a-zA-Z0-9]+$/'],
        ];
    }

    public function getSourceTypeList(): array
    {
        return CasesSourceType::getList();
    }

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

    public function validateCategory(): void
    {
        if (!array_key_exists($this->categoryId, $this->getCategories())) {
            $this->addError('categoryId', 'Category is Invalid');
        }
    }

    public function getCategories(): array
    {
        if ($this->depId) {
            return ArrayHelper::map((\Yii::createObject(CaseCategoryRepository::class))->getEnabledByDep($this->depId), 'cc_id', 'cc_name');
        }
        return [];
    }

    public function attributeLabels(): array
    {
        return [
            'projectId' => 'Project',
            'subject' => 'Subject',
            'description' => 'Description',
            'depId' => 'Department',
            'categoryId' => 'Category',
            'sourceTypeId' => 'Source type',
            'orderUid' => 'Booking ID',
        ];
    }
}
