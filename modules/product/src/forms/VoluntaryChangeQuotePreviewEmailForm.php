<?php

namespace modules\product\src\forms;

class VoluntaryChangeQuotePreviewEmailForm extends ProductPreviewEmailForm
{
    public $changeId;
    public $originQuoteId;

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
            [['changeId', 'originQuoteId'], 'required'],
            [['changeId', 'originQuoteId'], 'integer'],
        ];

        return array_merge(parent::rules(), $rules);
    }
}
