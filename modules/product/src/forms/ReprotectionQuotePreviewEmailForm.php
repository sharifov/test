<?php

namespace modules\product\src\forms;

class ReprotectionQuotePreviewEmailForm extends ProductPreviewEmailForm
{
    public $productQuoteId;
    public $orderId;
    public $pqcId;

    public function __construct(array $data = [], $config = [])
    {
        parent::__construct($data, $config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            [['productQuoteId', 'orderId', 'pqcId'], 'required'],
            [['productQuoteId', 'orderId', 'pqcId'], 'integer'],
        ];

        return array_merge(parent::rules(), $rules);
    }
}
