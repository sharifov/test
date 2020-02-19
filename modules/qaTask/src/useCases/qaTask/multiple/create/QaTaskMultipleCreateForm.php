<?php

namespace modules\qaTask\src\useCases\qaTask\multiple\create;

use frontend\widgets\multipleUpdate\IdsValidator;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use Webmozart\Assert\Assert;
use yii\base\Model;

/**
 * Class QaTaskMultipleCreateForm
 *
 * @property int[] $ids
 * @property int $objectType
 * @property int $categoryId
 * @property array $categoryList
 * @property int|null $userId
 */
class QaTaskMultipleCreateForm extends Model
{
    public $ids;
    public $objectType;
    public $categoryId;
    public $userId;

    private $categoryList;

    public function __construct(
        int $objectType,
        array $categoryList,
        ?int $userId,
        $config = []
    )
    {
        Assert::oneOf($objectType, array_keys(QaTaskObjectType::getList()));
        $this->objectType = $objectType;
        $this->userId = $userId;
        $this->categoryList = $categoryList;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['ids', IdsValidator::class, 'skipOnEmpty' => false],

            ['categoryId', 'required'],
            ['categoryId', 'integer'],
            ['categoryId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['categoryId', 'in', 'range' => array_keys($this->getCategoryList())],
        ];
    }

    public function getCategoryList(): array
    {
        return $this->categoryList;
    }

    public function attributeLabels(): array
    {
        return [
            'ids' => 'Ids',
            'categoryId' => 'Category',
        ];
    }

    public function convertIdsToString(): void
    {
        if (is_array($this->ids)) {
            $this->ids = implode(',', $this->ids);
        }
    }
}
