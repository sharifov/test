<?php

namespace modules\product\src\forms;

class VoluntaryRefundPreviewEmailForm extends ProductPreviewEmailForm
{
    public $productQuoteId;
    public $productQuoteRefundId;
    public $bookingId;

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
            [['productQuoteId', 'productQuoteRefundId', 'bookingId'], 'required'],
            [['productQuoteId'], 'integer'],
        ];

        return array_merge(parent::rules(), $rules);
    }
}
