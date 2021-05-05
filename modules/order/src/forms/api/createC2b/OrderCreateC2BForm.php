<?php

namespace modules\order\src\forms\api\createC2b;

use common\models\Sources;
use modules\order\src\entities\order\Order;
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
 * @property string $bookingId
 * @property string $fareId
 * @property string $status
 * @property int $sourceId
 * @property int $projectId
 * @property string|null $languageId
 * @property string|null $marketCountry
 * @property PaymentForm $payment
 * @property ContactsInfoForm[] $contactsInfo
 */
class OrderCreateC2BForm extends CompositeForm
{
    public $sourceCid;

    public $bookingId;

    public $status;

    public $fareId;

    public $sourceId;

    public $projectId;

    public $languageId;

    public $marketCountry;

    private const STATUS_SUCCESS = 'success';
    private const STATUS_FAILED = 'failed';

    private const ORDER_STATUS_RELATION = [
        self::STATUS_SUCCESS => OrderStatus::PROCESSING,
        self::STATUS_FAILED => OrderStatus::ERROR
    ];

    public function __construct($config = [])
    {
        $this->payment = new PaymentForm();
        parent::__construct($config);
    }

    public function load($data, $formName = null): bool
    {
        if (!empty($data['quotes']) && is_array($data['quotes'])) {
            $quotesForm = [];
            for ($i = 1, $iMax = count($data['quotes']); $i <= $iMax; $i++) {
                $quotesForm[] = new QuotesForm();
            }
            $this->quotes = $quotesForm;
        }

        if (!empty($data['creditCard'])) {
            $this->creditCard = new CreditCardForm();
        }

        if (!empty($data['billingInfo'])) {
            $this->billingInfo = new BillingInfoForm();
        }

        if (!empty($data['contactsInfo']) && is_array($data['contactsInfo'])) {
            $contactInfoForm = [];
            for ($i = 1, $iMax = count($data['contactsInfo']); $i <= $iMax; $i++) {
                $contactInfoForm[] = new ContactsInfoForm();
            }
            $this->contactsInfo = $contactInfoForm;
        }

        return parent::load($data, $formName);
    }

    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        if (!isset($this->quotes)) {
            $this->addError('quotes', 'Quotes are empty');
            return false;
        }

        if (!isset($this->contactsInfo)) {
            $this->addError('contactsInfo', 'Contact info is empty');
            return false;
        }

        return parent::validate($attributeNames, $clearErrors); // TODO: Change the autogenerated stub
    }

    public function rules(): array
    {
        return [
            [['sourceCid', 'bookingId', 'status', 'fareId'], 'required'],
            [['sourceCid', 'bookingId', 'status'], 'string', 'max' => 10],
            ['sourceCid', function () {
                if ($source = Sources::find()->select(['id', 'project_id'])->where(['cid' => $this->sourceCid])->asArray()->limit(1)->one()) {
                    $this->sourceId = $source['id'];
                    $this->projectId = $source['project_id'];
                } else {
                    $this->addError('sourceCid', 'Source not found');
                }
            }],
            [['bookingId'], 'string', 'max' => 7],
            [['fareId'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => [self::STATUS_SUCCESS, self::STATUS_FAILED]],
            ['fareId', 'unique', 'targetClass' => Order::class, 'targetAttribute' => 'or_fare_id'],

            ['languageId', 'safe'],

            ['marketCountry', 'safe'],
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
        return ['quotes', 'creditCard', 'billingInfo', 'payment', 'contactsInfo'];
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
