<?php

namespace modules\order\src\forms\api\create;

use modules\product\src\entities\productQuote\ProductQuote;
use sales\forms\CompositeRecursiveForm;
use yii\base\Model;

/**
 * Class OfferForm
 * @package modules\order\src\forms\api
 *
 * @property string $gid
 * @property ProductQuoteOptionsForm[] $productOptions
 */
class ProductQuotesForm extends CompositeRecursiveForm
{
    public string $gid = '';

    public function load($data, $formName = null, $forms = [])
    {
        $productOptionsForm = [];
        if (isset($data['productOptions']) && $productOptionsCount = count($data['productOptions'])) {
            for ($i = 1; $i <= $productOptionsCount; $i++) {
                $productOptionsForm[] = new ProductQuoteOptionsForm();
            }
        }
        $this->productOptions = $productOptionsForm;
        return parent::load($data, $formName, $forms);
    }

    public function rules(): array
    {
        return [
            ['gid', 'required'],
            ['gid', 'string'],
            ['gid', 'exist', 'targetClass' => ProductQuote::class, 'targetAttribute' => 'pq_gid']
        ];
    }

    public function formName(): string
    {
        return 'productQuotes';
    }

    /**
     * @inheritDoc
     */
    protected function internalForms(): array
    {
        return ['productOptions'];
    }
}
