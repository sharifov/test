<?php

namespace sales\forms\cases;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\Department;
use common\models\Employee;
use common\models\Project;
//use sales\entities\cases\Cases;
use sales\entities\cases\CasesCategory;
//use sales\entities\cases\CasesStatus;
use sales\entities\cases\CasesSourceType;
use yii\base\Model;
use yii\helpers\ArrayHelper;
//use yii\helpers\Html;

/**
 * Class CasesCreateByWebForm
 *
 * @property int $projectId
 * @property string $subject
 * @property string $description
 * @property string $category
 * @property string $clientPhone
 * @property string $clientEmail
 * @property int $depId
 * @property int $sourceTypeId
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
    public $clientEmail;
    public $depId;
    public $sourceTypeId;

    private $user;
    private $cache_projects;
    private $cache_departments;

    /**
     * CasesCreateByWebForm constructor.
     * @param Employee $tipsUser
     * @param array $config
     */
    public function __construct(Employee $tipsUser, $config = [])
    {
        parent::__construct($config);
        $this->user = $tipsUser;
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

            ['clientPhone', 'phoneOrEmailRequired', 'skipOnEmpty' => false],
            ['clientPhone', 'string', 'max' => 100],
            ['clientPhone', PhoneInputValidator::class],
            ['clientPhone', 'filter', 'filter' => function($value) {
                return str_replace('-', '', trim($value));
            }],
//			['clientPhone', 'checkPhoneForExistence']

            ['clientEmail', 'phoneOrEmailRequired', 'skipOnEmpty' => false],
            ['clientEmail', 'string', 'max' => 100],
            ['clientEmail', 'email'],
            ['clientEmail', 'filter', 'filter' => static function($value) {
                return mb_strtolower(trim($value));
            }],
            
            ['sourceTypeId', 'required'],
            ['sourceTypeId', 'integer'],
            ['sourceTypeId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['sourceTypeId', 'in', 'range' => array_keys($this->getSourceTypeList())],
        ];
    }

    public function getSourceTypeList(): array
    {
        return CasesSourceType::getList();
    }

    public function phoneOrEmailRequired(): void
    {
        if (!$this->clientPhone && !$this->clientEmail) {
            $this->addError('clientPhone', 'Phone or Email cannot be blank.');
            $this->addError('clientEmail', 'Email or Phone cannot be blank.');
            return;
        }
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

//	/**
//	 * @return bool
//	 */
//	public function checkPhoneForExistence()
//	{
//		$cases = Cases::find()
//			->join('join', 'client_phone', 'cs_client_id = client_id and phone = :phone', ['phone' => $this->clientPhone])
//			->where(['cs_status' => [CasesStatus::STATUS_PENDING, CasesStatus::STATUS_PROCESSING, CasesStatus::STATUS_FOLLOW_UP]])
//			->all();
//
//		if ($cases) {
//			$casesLink = '';
//			foreach ($cases as $case) {
//				$casesLink .= Html::a('Case ' . $case->cs_id, '/cases/view/' . $case->cs_gid, ['target' => '_blank']) . ' ';
//			}
//			$this->addError('clientPhone', 'This number is already used in ' . $casesLink);
//			return false;
//		}
//
//		return true;
//	}

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
            'clientEmail' => 'Email',
            'sourceTypeId' => 'Source type',
        ];
    }

}