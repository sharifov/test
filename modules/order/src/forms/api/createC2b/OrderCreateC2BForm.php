<?php

namespace modules\order\src\forms\api\createC2b;

use sales\forms\CompositeForm;

/**
 * Class CreateC2BForm
 * @package modules\order\src\forms\createC2b
 *
 * @property QuotesForm[] $quotes
 * @property CreditCardForm $creditCard
 * @property BillingInfoForm $billingInfo
 * @property string $sourceCid
 * @property string $requestUid
 */
class OrderCreateC2BForm extends CompositeForm
{
    public $sourceCid;

    public $requestUid;

    public function __construct(int $cntQuotes, bool $creditCardForm = false, bool $billingInfoForm = false, $config = [])
    {
        $quotesForm = [];
        for ($i = 1; $i <= $cntQuotes; $i++) {
            $quotesForm[] = new QuotesForm();
        }
        $this->quotes = $quotesForm;
        if ($creditCardForm) {
            $this->creditCard = new CreditCardForm();
        }
        if ($billingInfoForm) {
            $this->billingInfo = new BillingInfoForm();
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['sourceCid', 'requestUid'], 'required'],
            [['sourceCid', 'requestUid'], 'string', 'max' => 10]
        ];
    }

    public function formName(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    protected function internalForms(): array
    {
        return ['quotes', 'creditCard', 'billingInfo'];
    }
}
