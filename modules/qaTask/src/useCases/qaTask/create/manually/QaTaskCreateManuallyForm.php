<?php

namespace modules\qaTask\src\useCases\qaTask\create\manually;

use yii\base\Model;

/**
 * Class QaTaskCreateManuallyForm
 *
 * @property int $objectType
 * @property int $objectId
 * @property int $projectId
 * @property int $departmentId
 * @property array $categoryList
 * @property int $categoryId
 * @property int $createdUserId
 * @property string|null $description
 */
class QaTaskCreateManuallyForm extends Model
{
    public $objectType;
    public $objectId;
    public $projectId;
    public $departmentId;
    public $categoryList;
    public $createdUserId;

    public $categoryId;
    public $description;

    public function __construct(
        int $objectType,
        int $objectId,
        ?int $projectId,
        ?int $departmentId,
        array $categoryList,
        int $createdUserId,
        $config = []
    )
    {
        $this->objectType = $objectType;
        $this->objectId = $objectId;
        $this->projectId = $projectId;
        $this->departmentId = $departmentId;
        $this->categoryList = $categoryList;
        $this->createdUserId = $createdUserId;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['categoryId', 'required'],
            ['categoryId', 'integer'],
            ['categoryId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['categoryId', 'in', 'range' => array_keys($this->getCategoryList())],

            ['description', 'string', 'max' => 255],
            ['description', 'default', 'value' => null],
        ];
    }

    public function getCategoryList(): array
    {
        return $this->categoryList;
    }

    public function attributeLabels(): array
    {
        return [
            'categoryId' => 'Category',
        ];
    }
}
