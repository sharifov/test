<?php

namespace modules\order\src\forms\api\create;

use common\components\validators\CheckJsonValidator;
use modules\product\src\entities\productOption\ProductOption;
use yii\base\Model;

class ProductQuoteOptionsForm extends Model
{
    public $productOptionKey;

    public $name;

    public $description;

    public $price;

    public $json_data;

    public function rules()
    {
        return [
            ['productOptionKey', 'string'],
            ['productOptionKey', 'required'],
            ['productOptionKey', 'exist', 'targetClass' => ProductOption::class, 'targetAttribute' => 'po_key', 'message' => 'Not found Product Option by key.'],
            ['name', 'string', 'max' => 50],
            ['description', 'safe'],
            ['price', 'required'],
            ['price', 'number'],
            ['price', 'filter', 'filter' => 'floatval', 'skipOnEmpty' => true],
            ['json_data', CheckJsonValidator::class, 'skipOnEmpty' => true]
        ];
    }

    public function formName(): string
    {
        return 'productOptions';
    }
}
