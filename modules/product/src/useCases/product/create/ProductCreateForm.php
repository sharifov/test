<?php

namespace modules\product\src\useCases\product\create;

use common\models\Lead;
use modules\product\src\entities\product\dto\CreateDto;
use modules\product\src\guards\ProductAvailableGuard;
use src\access\EmployeeProductAccess;
use yii\base\Model;

/**
 * Class ProductCreateForm
 *
 * @property int $pr_lead_id
 * @property int $pr_type_id
 * @property string $pr_name
 * @property string $pr_description
 * @property int $pr_project_id
 *
 * @property bool $isProductAvailableGuard
 */
class ProductCreateForm extends Model
{
    public $pr_lead_id;
    public $pr_type_id;
    public $pr_name;
    public $pr_description;
    public $pr_project_id;

    private bool $isProductAvailableGuard = true;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['pr_lead_id', 'required'],
            ['pr_lead_id', 'integer'],
            ['pr_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['pr_lead_id' => 'id']],

            ['pr_type_id', 'required'],
            ['pr_type_id', 'integer'],
            ['pr_type_id', 'filter', 'filter' => 'intval'],
            ['pr_type_id', 'in', 'range' => (new EmployeeProductAccess(\Yii::$app->user))->getProductListId()],
            ['pr_type_id', function () {
                try {
                    if ($this->isProductAvailableGuard) {
                        ProductAvailableGuard::check($this->pr_type_id);
                    }
                } catch (\DomainException $e) {
                    $this->addError('pr_type_id', $e->getMessage());
                }
            }, 'skipOnEmpty' => true, 'skipOnError' => true],

            ['pr_name', 'string', 'max' => 40],

            ['pr_description', 'string'],
            ['pr_project_id', 'integer']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'pr_lead_id' => 'Lead',
            'pr_type_id' => 'Product Type',
            'pr_name' => 'Name',
            'pr_description' => 'Description',
        ];
    }

    public function getDto(): CreateDto
    {
        return new CreateDto($this->pr_lead_id, $this->pr_type_id, $this->pr_name, $this->pr_description, $this->pr_project_id);
    }

    /**
     * @param bool $isProductAvailableGuard
     * @return ProductCreateForm
     */
    public function setIsProductAvailableGuard(bool $isProductAvailableGuard): ProductCreateForm
    {
        $this->isProductAvailableGuard = $isProductAvailableGuard;
        return $this;
    }
}
