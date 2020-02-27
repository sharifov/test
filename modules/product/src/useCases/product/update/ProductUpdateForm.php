<?php

namespace modules\product\src\useCases\product\update;

use modules\product\src\entities\product\Product;
use sales\yii\validators\NumberValidator;
use yii\base\Model;

/**
 * Class ProductUpdateForm
 *
 * @property string $pr_name
 * @property string $pr_description
 * @property int $productId
 * @property int $pr_market_price
 * @property int $pr_client_budget
 */
class ProductUpdateForm extends Model
{
    public $pr_name;
    public $pr_description;
    public $productId;
    public $pr_market_price;
    public $pr_client_budget;

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
            ['pr_name', 'string', 'max' => 40],

            ['pr_description', 'string'],

            ['pr_market_price', 'default', 'value' => null],
            ['pr_market_price', 'number', 'numberPattern' => NumberValidator::PRICE_PATTERN, 'max' => 999999],

            ['pr_client_budget', 'default', 'value' => null],
            ['pr_client_budget', 'number', 'numberPattern' => NumberValidator::PRICE_PATTERN, 'max' => 999999],
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
            'pr_market_price' => 'Market price',
            'pr_client_budget' => 'Client budget',
        ];
    }
}
