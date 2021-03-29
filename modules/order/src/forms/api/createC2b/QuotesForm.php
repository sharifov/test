<?php

namespace modules\order\src\forms\createC2b;

use modules\product\src\entities\productType\ProductType;

class QuotesForm extends \yii\base\Model
{
    public $productKey;

    public $originSearchData;

    public $paxData;

    public function rules()
    {
        return [
            [['productKey', 'originSearchData', 'paxData'], 'required'],
            ['productKey', 'string'],
            [['originSearchData', 'paxData'], 'safe'],
            [['productKey'], 'exist', 'targetClass' => ProductType::class, 'targetAttribute' => 'pt_key']
        ];
    }
}
