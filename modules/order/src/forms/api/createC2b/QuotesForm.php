<?php

namespace modules\order\src\forms\api\createC2b;

use common\components\validators\CheckJsonValidator;
use modules\product\src\entities\productType\ProductType;

/**
 * Class QuotesForm
 * @package modules\order\src\forms\api\createC2b
 *
 * @property string $productKey
 * @property string $originSearchData
 * @property string $paxData
 * @property string $quoteOtaId
 * @property int $orderId
 */
class QuotesForm extends \yii\base\Model
{
    public $productKey;

    public $originSearchData;

    public $paxData;

    public $quoteOtaId;

    public $orderId;

    public function rules(): array
    {
        return [
            [['productKey', 'originSearchData', 'paxData', 'quoteOtaId'], 'required'],
            [['productKey', 'quoteOtaId'], 'string'],
            [['productKey'], 'exist', 'targetClass' => ProductType::class, 'targetAttribute' => 'pt_key'],
            [['originSearchData', 'paxData'], CheckJsonValidator::class],
        ];
    }

    public function formName(): string
    {
        return "quotes";
    }
}
