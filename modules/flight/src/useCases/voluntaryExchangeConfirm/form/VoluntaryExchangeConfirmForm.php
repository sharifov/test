<?php

namespace modules\flight\src\useCases\voluntaryExchangeConfirm\form;

use common\components\validators\CheckAndConvertToJsonValidator;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use src\entities\cases\Cases;
use src\exception\ValidationException;
use src\helpers\ErrorsToStringHelper;
use src\helpers\setting\SettingHelper;
use webapi\src\forms\billing\BillingInfoForm;
use webapi\src\forms\payment\PaymentRequestForm;
use webapi\src\logger\behaviors\filters\creditCard\CreditCardFilter;
use yii\base\Model;

/**
 * Class VoluntaryExchangeConfirmForm
 *
 * @property $booking_id
 * @property $payment_request
 * @property $billing
 * @property $quote_gid
 *
 * @property PaymentRequestForm|null $paymentRequestForm
 * @property BillingInfoForm|null $billingInfoForm
 * @property ProductQuote|null $changeQuote
 * @property ProductQuote|null $originQuote
 * @property ProductQuoteChange|null $productQuoteChange
 * @property Cases $case
 */
class VoluntaryExchangeConfirmForm extends Model
{
    public const PQC_ALLOW_STATUS_LIST = [
        ProductQuoteChangeStatus::NEW,
        ProductQuoteChangeStatus::PENDING,
        ProductQuoteChangeStatus::IN_PROGRESS,
        ProductQuoteChangeStatus::ERROR,
    ];

    public const PQ_ALLOW_STATUS_LIST = [
        ProductQuoteStatus::NEW,
        ProductQuoteStatus::PENDING,
        ProductQuoteStatus::IN_PROGRESS,
        ProductQuoteStatus::APPLIED,
        ProductQuoteStatus::ERROR,
    ];

    public $booking_id;
    public $quote_gid;

    public $payment_request;
    public $billing;

    private ?PaymentRequestForm $paymentRequestForm = null;
    private ?BillingInfoForm $billingInfoForm = null;

    private ?ProductQuote $changeQuote = null;
    private ?ProductQuote $originQuote = null;
    private ?ProductQuoteChange $productQuoteChange = null;
    private ?Cases $case = null;

    public function rules(): array
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id'], 'string', 'max' => 10],

            [['quote_gid'], 'required'],
            [['quote_gid'], 'string', 'max' => 32],
            [['quote_gid'], 'quoteProcessing'],

            [['payment_request'], CheckAndConvertToJsonValidator::class, 'skipOnEmpty' => true, 'skipOnError' => true],
            [['payment_request'], 'paymentRequestProcessing'],

            [['billing'], CheckAndConvertToJsonValidator::class, 'skipOnEmpty' => true, 'skipOnError' => true],
            [['billing'], 'billingProcessing'],
        ];
    }

    public function quoteProcessing(string $attribute): void
    {
        try {
            if (!$this->changeQuote = ProductQuote::findOne(['pq_gid' => $this->quote_gid])) {
                throw new ValidationException('ProductQuote not found');
            }
            if (!in_array($this->changeQuote->pq_status_id, SettingHelper::getExchangeQuoteConfirmStatusList(), false)) {
                $processingList = ProductQuoteStatus::getNames(SettingHelper::getExchangeQuoteConfirmStatusList());
                throw new ValidationException('ProductQuote not in processing statuses(' . implode(',', $processingList) . '). Current status(' .
                    ProductQuoteStatus::getName($this->changeQuote->pq_status_id) . ')');
            }
            if (!$this->productQuoteChange = $this->changeQuote->productQuoteChangeLastRelation->pqcrPqc ?? null) {
                throw new ValidationException('ProductQuoteChange not found');
            }
            if (!in_array($this->productQuoteChange->pqc_status_id, self::PQC_ALLOW_STATUS_LIST, false)) {
                $processingPQCList = implode(',', ProductQuoteChangeStatus::getNames(self::PQC_ALLOW_STATUS_LIST));
                throw new ValidationException('ProductQuoteChange not in processing statuses(' . $processingPQCList . '). Current status(' .
                    ProductQuoteChangeStatus::getName($this->productQuoteChange->pqc_status_id) . ')');
            }
            if (!$this->case = $this->productQuoteChange->pqcCase ?? null) {
                throw new ValidationException('Case not found');
            }
            if (!$this->originQuote = ProductQuoteQuery::getOriginProductQuoteByChangeQuote($this->changeQuote->pq_id)) {
                throw new ValidationException('Origin Quote not found');
            }
            if (!($this->originQuote->isBooked() || $this->originQuote->isSold())) {
                throw new ValidationException('Origin Quote in status(Booked,Sold). Current status(' .
                    ProductQuoteStatus::getName($this->originQuote->pq_status_id) . ')');
            }
        } catch (\Throwable $throwable) {
            $this->addError($attribute, $throwable->getMessage());
        }
    }

    public function paymentRequestProcessing(string $attribute): void
    {
        if (!empty($this->payment_request)) {
            $paymentRequestForm = new PaymentRequestForm();
            $paymentRequestForm->setFormName('');
            if (!$paymentRequestForm->load($this->payment_request)) {
                $this->addError($attribute, 'PaymentRequestForm is not loaded');
            } elseif (!$paymentRequestForm->validate()) {
                $this->addError($attribute, 'PaymentRequestForm: ' . ErrorsToStringHelper::extractFromModel($paymentRequestForm, ' '));
            } else {
                $this->paymentRequestForm = $paymentRequestForm;
            }
        }
    }

    public function billingProcessing(string $attribute): void
    {
        if (!empty($this->billing)) {
            $billingInfoForm = new BillingInfoForm();
            $billingInfoForm->setFormName('');
            if (!$billingInfoForm->load($this->billing)) {
                $this->addError($attribute, 'BillingInfoForm is not loaded');
            } elseif (!$billingInfoForm->validate()) {
                $this->addError($attribute, 'BillingInfoForm: ' . ErrorsToStringHelper::extractFromModel($billingInfoForm, ' '));
            } else {
                $this->billingInfoForm = $billingInfoForm;
            }
        }
    }

    public function formName(): string
    {
        return '';
    }

    public function getPaymentRequestForm(): ?PaymentRequestForm
    {
        return $this->paymentRequestForm;
    }

    public function getBillingInfoForm(): ?BillingInfoForm
    {
        return $this->billingInfoForm;
    }

    public function getChangeQuote(): ProductQuote
    {
        return $this->changeQuote;
    }

    public function getOriginQuote(): ProductQuote
    {
        return $this->originQuote;
    }

    public function getProductQuoteChange(): ProductQuoteChange
    {
        return $this->productQuoteChange;
    }

    public function getCase(): Cases
    {
        return $this->case;
    }

    public function getFilteredData(): array
    {
        return (\Yii::createObject(CreditCardFilter::class))->filterData($this->toArray());
    }
}
