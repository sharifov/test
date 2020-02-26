<?php

namespace modules\product\src\forms;

use modules\product\src\entities\product\Product;
use yii\base\Model;

/**
 * Class ProductUpdateForm
 *
 * @property string $pr_name
 * @property string $pr_description
 * @property int $productId
 */
class ProductUpdateForm extends Model
{
    public $pr_name;
    public $pr_description;
    public $productId;

    public function __construct(Product $product, $config = [])
    {
        parent::__construct($config);
        $this->productId = $product->pr_id;
        $this->load($product->getAttributes(), '');
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['pr_name', 'required'],
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
            'pr_name' => 'Name',
            'pr_description' => 'Description',
        ];
    }
}
