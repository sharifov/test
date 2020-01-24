<?php

namespace modules\product\src\useCases\product\create;

use common\models\Lead;
use common\models\ProductType;
use modules\product\src\guards\ProductAvailableGuard;
use yii\base\Model;

/**
 * Class ProductCreateForm
 *
 * @property int $pr_lead_id
 * @property int $pr_type_id
 * @property string $pr_name
 * @property string $pr_description
 */
class ProductCreateForm extends Model
{
    public $pr_lead_id;
    public $pr_type_id;
    public $pr_name;
    public $pr_description;

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
            ['pr_type_id', 'in', 'range' => [ProductType::PRODUCT_FLIGHT, ProductType::PRODUCT_HOTEL]],
            ['pr_type_id', function () {
                try {
                    ProductAvailableGuard::check($this->pr_type_id);
                } catch (\DomainException $e) {
                    $this->addError('pr_type_id', $e->getMessage());
                }
            }, 'skipOnEmpty' => true, 'skipOnError' => true],

            ['pr_name', 'string', 'max' => 40],

            ['pr_description', 'string'],
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
}
