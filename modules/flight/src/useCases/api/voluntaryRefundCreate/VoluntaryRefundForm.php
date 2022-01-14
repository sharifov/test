<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

use common\components\validators\CheckIsNumberValidator;
use common\components\validators\IsArrayValidator;
use common\models\Currency;
use src\helpers\ErrorsToStringHelper;
use yii\base\Model;

/**
 * Class VoluntaryRefundForm
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate
 *
 * @property string $currency
 * @property array $tickets
 * @property array $auxiliaryOptions
 * @property float $processingFee
 * @property float $penaltyAmount
 * @property float $totalRefundAmount
 * @property float $totalPaid
 * @property string $orderId
 *
 * @property TicketForm[] $ticketForms
 * @property AuxiliaryOptionForm[] $auxiliaryOptionsForms
 */
class VoluntaryRefundForm extends Model
{
    public $currency;
    public $processingFee;
    public $penaltyAmount;
    public $totalRefundAmount;
    public $totalPaid;
    public $tickets;
    public $auxiliaryOptions;
    public $orderId;

    /**
     * @var TicketForm[] $ticketForms
     */
    public array $ticketForms = [];

    /**
     * @var AuxiliaryOptionForm[] $auxiliaryOptionsForms
     */
    public array $auxiliaryOptionsForms = [];

    public function rules(): array
    {
        return [
            [['currency', 'processingFee', 'penaltyAmount', 'totalRefundAmount', 'totalPaid', 'orderId'], 'required'],
            ['currency', 'string', 'max' => 3],
            ['currency', 'exist', 'targetClass' => Currency::class, 'targetAttribute' => 'cur_code'],

            [['processingFee', 'penaltyAmount', 'totalRefundAmount', 'totalPaid'], 'number'],
            [['processingFee', 'penaltyAmount', 'totalRefundAmount', 'totalPaid'], CheckIsNumberValidator::class, 'allowInt' => true],

            ['tickets', IsArrayValidator::class, 'skipOnEmpty' => false],
            ['tickets', 'ticketsValidation', 'skipOnEmpty' => false],

            ['auxiliaryOptions', IsArrayValidator::class, 'skipOnEmpty' => true],
            ['auxiliaryOptions', 'auxiliaryOptionsValidation', 'skipOnEmpty' => true],

            ['orderId', 'string', 'max' => 32]
        ];
    }

    public function formName(): string
    {
        return '';
    }

    public function ticketsValidation(string $attribute): bool
    {
        if (empty($this->tickets)) {
            $this->addError($attribute, 'Tickets data not provided');
            return false;
        }

        foreach ($this->tickets as $key => $ticket) {
            $ticketForm = new TicketForm();
            $ticketForm->load($ticket, '');
            if (!$ticketForm->validate()) {
                $this->addError('tickets' . '.' . $key, 'Refund.tickets.' . $key . ': ' . ErrorsToStringHelper::extractFromModel($ticketForm, ', '));
                return false;
            }

            $this->ticketForms[] = $ticketForm;
        }

        return true;
    }

    public function auxiliaryOptionsValidation(string $attribute): bool
    {
        if (empty($this->auxiliaryOptions)) {
            $this->addError($attribute, 'Tickets data not provided');
            return false;
        }

        foreach ($this->auxiliaryOptions as $key => $auxiliaryOption) {
            if (!is_array($auxiliaryOption)) {
                $this->addError('auxiliaryOptions' . '.' . $key, 'Refund.auxiliaryOptions.' . $key . ': is not array');
            }
            $auxiliaryOptionForm = new AuxiliaryOptionForm();
            $auxiliaryOptionForm->load($auxiliaryOption, '');
            if (!$auxiliaryOptionForm->validate()) {
                $this->addError('auxiliaryOptions' . '.' . $key, 'Refund.auxiliaryOptions.' . $key . ': ' . ErrorsToStringHelper::extractFromModel($auxiliaryOptionForm, ', '));
                return false;
            }

            $this->auxiliaryOptionsForms[] = $auxiliaryOptionForm;
        }

        return true;
    }
}
