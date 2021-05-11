<?php

namespace  webapi\src\forms\flight\options;

use common\components\validators\CheckJsonValidator;
use modules\product\src\entities\productOption\ProductOption;
use modules\product\src\entities\productOption\ProductOptionRepository;
use modules\product\src\entities\productOption\serializer\ProductOptionSerializer;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\exceptions\ProductCodeException;
use yii\base\Model;

/**
 * Class InsuranceApiForm
 *
 * @property $pqo_key
 * @property $pqo_name
 * @property $pqo_price
 * @property $pqo_markup
 * @property $pqo_description
 * @property $pqo_request_data
 *
 * @property ProductOption $productOption
 */
class OptionApiForm extends Model
{
    public $pqo_key;
    public $pqo_name;
    public $pqo_price;
    public $pqo_markup;
    public $pqo_description;
    public $pqo_request_data;

    private $productOption;

    public function rules(): array
    {
        return [
            [['pqo_key', 'pqo_name', 'pqo_description', 'pqo_price', 'pqo_markup'], 'required'],

            [['pqo_key'], 'string'],
            [['pqo_key'], 'checkProductOption'],

            [['pqo_name'], 'string', 'max' => 50],

            [['pqo_description'], 'string'],

            [['pqo_price', 'pqo_markup'], 'number'],

            [['pqo_request_data'], CheckJsonValidator::class],
        ];
    }

    public function checkProductOption($attribute)
    {
        try {
            if (!$this->productOption = $this->findOrCreate($this->pqo_key, $this->pqo_name, ProductType::PRODUCT_FLIGHT)) {
                $this->addError($attribute, 'ProductOption not found by key (' . $this->pqo_key . ')');
            }
        } catch (\Throwable $throwable) {
            $this->addError($attribute, $throwable->getMessage());
        }
    }

    public function formName(): string
    {
        return '';
    }

    private function findOrCreate(string $key, ?string $name = null, ?int $productTypeId = null): ProductOption
    {
        if ($productOption = ProductOption::findOne(['po_key' => $key])) {
            return $productOption;
        }
        if ($name !== null && $productTypeId !== null) {
            $productOption = ProductOption::create($key, $name, $productTypeId);
            $productOption->detachBehavior('user');
            if ($productOption->validate()) {
                $productOption->save();
            }
        }
        return $productOption;
    }

    public function getProductOption(): ProductOption
    {
        return $this->productOption;
    }
}
