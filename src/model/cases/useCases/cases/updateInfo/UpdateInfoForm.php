<?php

namespace src\model\cases\useCases\cases\updateInfo;

use common\models\Department;
use common\models\Employee;
use src\entities\cases\CaseCategory;
use src\entities\cases\Cases;
use src\entities\cases\CasesSourceType;
use yii\base\Model;

/**
 * Class CasesUpdateForm
 *
 * @property Cases $case
 * @property int $depId
 * @property int $categoryId
 * @property string $subject
 * @property string $description
 * @property string|null $orderUid
 * @property array $departmentList
 * @property array $categoryList
 */
class UpdateInfoForm extends Model
{
    public $depId;
    public $categoryId;
    public $subject;
    public $description;
    public $orderUid;
    public $userId;
    public FieldAccess $fieldAccess;

    private $case;
    private $categoryList = [];
    private $departmentList;

    public function __construct(
        Cases $case,
        array $departmentList,
        array $categoryList,
        int $userId,
        FieldAccess $fieldAccess,
        $config = []
    ) {
        parent::__construct($config);
        $this->case = $case;
        $this->depId = $case->cs_dep_id;
        $this->departmentList = $departmentList;
        $this->categoryId = $case->cs_category_id;
        $this->orderUid = $case->cs_order_uid;
        $this->subject = $case->cs_subject;
        $this->description = $case->cs_description;
        $this->categoryList = $categoryList;
        $this->userId = $userId;
        $this->fieldAccess = $fieldAccess;
    }

    public function rules(): array
    {
        return [
            ['depId', 'required'],
            ['depId', 'integer'],
            ['depId', 'in', 'range' => array_keys($this->getdepartmentList())],
            ['depId', 'validateDepartment'],

            ['categoryId', 'required'],
            ['categoryId', 'integer'],
            ['categoryId', 'in', 'range' => function () {
                return $this->getAvailableCategories($this->depId);
            }],
            ['categoryId', 'validateCategory'],

            ['subject', 'string', 'max' => 200],
            ['userId', 'integer'],

            ['description', 'string'],
            ['description', 'validateDescription'],

            ['orderUid', 'default', 'value' => null],
            ['orderUid', 'string', 'min' => '5', 'max' => 7],
            ['orderUid', 'match', 'pattern' => '/^[a-zA-Z0-9]+$/'],
        ];
    }

    public function validateDepartment($attribute)
    {
        if (!$this->fieldAccess->canEditDepartment() && $this->depId !== $this->case->getDepartmentId()) {
            $this->addError($attribute, 'The value is not acceptable.');
        }

    }

    public function validateCategory($attribute)
    {
        if (!$this->fieldAccess->canEditCategory() && $this->categoryId !== $this->case->cs_category_id) {
            $this->addError($attribute, 'The value is not acceptable.');
        }

    }

    public function validateDescription($attribute)
    {
        if (!$this->fieldAccess->canEditDescription() && $this->description !== $this->case->cs_description) {
            $this->addError($attribute, 'The value is not acceptable.');
        }

    }


    public function attributeLabels(): array
    {
        return [
            'depId' => 'Department',
            'categoryId' => 'Category',
            'subject' => 'Subject',
            'description' => 'Description',
            'orderUid' => 'Booking ID',
        ];
    }

    public function getCategoryList(): array
    {
        return $this->categoryList;
    }

    public function getCaseGid(): string
    {
        return $this->case->cs_gid;
    }

    public function getDto(): Command
    {
        return new Command(
            $this->case->cs_id,
            $this->depId,
            $this->categoryId,
            $this->subject,
            $this->description,
            $this->orderUid,
            $this->userId
        );
    }

    public function getdepartmentList()
    {
        return Department::getList();
    }

    private function getAvailableCategories(int $depId)
    {
        $categories = CaseCategory::find()->select(['cc_id'])->andWhere(['cc_dep_id' => $depId])->asArray()->all();
        return array_column($categories, 'cc_id');
    }
}
