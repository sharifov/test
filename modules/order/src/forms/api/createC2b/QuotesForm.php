<?php

namespace modules\order\src\forms\api\createC2b;

use common\components\validators\CheckJsonValidator;
use modules\product\src\entities\productType\ProductType;
use sales\forms\CompositeForm;

/**
 * Class QuotesForm
 * @package modules\order\src\forms\api\createC2b
 *
 * @property string $productKey
 * @property string $originSearchData
 * @property string $paxData
 * @property string $quoteOtaId
 * @property int $orderId
 *
 * @property ProductHolderForm $holder
 */
class QuotesForm extends CompositeForm
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

    public function load($data, $formName = null)
    {
        $this->holder = new ProductHolderForm();
        return parent::load($data, $formName);
    }

    public function formName(): string
    {
        return "quotes";
    }

    /**
     * @inheritDoc
     */
    protected function internalForms()
    {
        return ['holder'];
    }
}
