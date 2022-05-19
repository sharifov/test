<?php

namespace modules\flight\src\useCases\voluntaryRefund\manualCreate;

use common\components\validators\NormalizeDateValidator;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use src\entities\cases\Cases;
use src\helpers\ErrorsToStringHelper;
use yii\base\Model;
use common\components\validators\CheckIsBooleanValidator;

/**
 * Class VoluntaryRefundCreateForm
 *
 * @property bool $refundDataReadOnly
 * @property bool $ticketDataReadOnly
 * @property bool $allow
 */
class VoluntaryRefundCreateForm extends Model
{
    public $bookingId;
    public $allow;
    public $airlineAllow;
    public $automatic;
    public $refund;

    public $originProductQuoteId;
    public $orderId;
    public $caseId;

    public bool $refundDataReadOnly = true;
    public bool $ticketDataReadOnly = true;

    private ?VoluntaryRefundForm $refundForm = null;

    public array $originData = [];
    public ?string $expirationDate = null;

    public function rules(): array
    {
        return [
            [['bookingId', 'expirationDate'], 'required'],
            [['bookingId'], 'string', 'max' => 50],

//            [['allow', 'airlineAllow', 'automatic'], 'filter', 'filter' => 'floatval', 'skipOnEmpty' => true],
//            [['allow', 'airlineAllow', 'automatic'], 'number'],
            [['allow', 'airlineAllow', 'automatic'], CheckIsBooleanValidator::class],

            [['refund'], 'safe'],
            [['refund'], 'refundProcessing', 'skipOnEmpty' => false],

            [['originProductQuoteId', 'orderId', 'caseId'], 'integer'],
            [['caseId'], 'exist', 'targetClass' => Cases::class, 'targetAttribute' => ['caseId' => 'cs_id'], 'skipOnError' => true],
            ['expirationDate', NormalizeDateValidator::class],
            ['expirationDate', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function load($data, $formName = null)
    {
        $refundForm = new VoluntaryRefundForm();
        $refundForm->load($data);
        $this->refundForm = $refundForm;
        return parent::load($data, $formName);
    }

    public function refundProcessing(string $attribute, $params, $validator, $data): bool
    {
        if ($this->refundForm && !$this->refundForm->validate()) {
            $this->addError($attribute, 'Refund: ' . ErrorsToStringHelper::extractFromModel($this->refundForm, ', '));
            return false;
        }
        return true;
    }

    public function formName(): string
    {
        return '';
    }

    public function getRefundForm(): ?VoluntaryRefundForm
    {
        return $this->refundForm;
    }

    public function disableReadOnlyAllFields(): void
    {
        $this->refundDataReadOnly = false;
        $this->ticketDataReadOnly = false;
    }

    /**
     * @param string $date
     * @return void
     */
    public function setExpirationDate(string $date): void
    {
        $this->expirationDate = $date;
    }
}
