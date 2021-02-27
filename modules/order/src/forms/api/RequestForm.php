<?php

namespace modules\order\src\forms\api;

use sales\forms\CompositeForm;
use sales\forms\CompositeRecursiveForm;

/**
 * Class RequestForm
 * @package modules\order\src\forms\api
 *
 * @property PaymentForm $Payment
 */
class RequestForm extends CompositeRecursiveForm
{
    public $offerGid;

    public function rules()
    {
        return [
            ['offerGid', 'string'],
            ['offerGid', 'required'],
        ];
    }

    public function __construct($config = [])
    {
        $this->Payment = new PaymentForm();
        parent::__construct($config);
    }

    /**
     * @inheritDoc
     */
    public function formName()
    {
        return 'Request';
    }

    /**
     * @inheritDoc
     */
    protected function internalForms(): array
    {
        return ['Payment'];
    }
}
