<?php

namespace modules\order\src\forms\api\createC2b;

use modules\order\src\entities\order\OrderStatus;
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
 * @property string $status
 * @property PaymentForm $payment
 */
class OrderCreateC2BForm extends CompositeForm
{
    public $sourceCid;

    public $requestUid;

    public $status;

    private const STATUS_SUCCESS = 'success';
    private const STATUS_FAILED = 'failed';

    private const ORDER_STATUS_RELATION = [
        self::STATUS_SUCCESS => OrderStatus::PROCESSING,
        self::STATUS_FAILED => OrderStatus::ERROR
    ];

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
        $this->payment = new PaymentForm();
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['sourceCid', 'requestUid', 'status'], 'required'],
            [['sourceCid', 'requestUid', 'status'], 'string', 'max' => 10],
            [['status'], 'in', 'range' => [self::STATUS_SUCCESS, self::STATUS_FAILED]]
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
        return ['quotes', 'creditCard', 'billingInfo', 'payment'];
    }

    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function getOrderStatus(): int
    {
        return self::ORDER_STATUS_RELATION[$this->status] ?? OrderStatus::PROCESSING;
    }
}
