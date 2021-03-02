<?php

namespace modules\order\src\forms\api\create;

use modules\product\src\entities\productOption\ProductOption;
use yii\base\Model;

class ProductQuoteOptionsForm extends Model
{
    public $productOptionKey;

    public $name;

    public $description;

    public $price;

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
        ];
    }

    public function formName(): string
    {
        return 'quoteOptions';
    }
}
