<?php

namespace modules\flight\src\useCases\voluntaryRefund\manualCreate;

use common\components\validators\CheckIsNumberValidator;
use common\components\validators\IsArrayValidator;
use common\models\Currency;
use sales\helpers\ErrorsToStringHelper;
use yii\base\Model;

/**
 * Class VoluntaryRefundForm
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate
 *
 * @property string $currency
 * @property array $tickets
 * @property array $auxiliaryOptions
 * @property float $totalProcessingFee
 * @property float $totalAirlinePenalty
 * @property float $totalRefundable
 * @property float $totalPaid
 * @property float $refundCost
 *
 * @property TicketForm[] $ticketForms
 * @property AuxiliaryOptionForm[] $auxiliaryOptionsForms
 */
class VoluntaryRefundForm extends Model
{
    public $currency;
    public $totalProcessingFee;
    public $totalAirlinePenalty;
    public $totalRefundable;
    public $totalPaid;
    public $refundCost;
    public $tickets;
    public $auxiliaryOptions;

    /**
     * @var TicketForm[] $ticketForms
     */
    private array $ticketForms = [];

    /**
     * @var AuxiliaryOptionForm[] $auxiliaryOptionsForms
     */
    private array $auxiliaryOptionsForms = [];

    public function rules(): array
    {
        return [
            [['currency', 'totalProcessingFee', 'totalAirlinePenalty', 'totalRefundable', 'totalPaid'], 'required'],
            ['currency', 'string', 'max' => 3],
            ['currency', 'exist', 'targetClass' => Currency::class, 'targetAttribute' => 'cur_code'],

            [['totalProcessingFee', 'totalAirlinePenalty', 'totalRefundable', 'totalPaid', 'refundCost'], 'number'],
            [['totalProcessingFee', 'totalAirlinePenalty', 'totalRefundable', 'totalPaid', 'refundCost'], 'filter', 'filter' => 'floatval'],
            [['totalProcessingFee', 'totalAirlinePenalty', 'totalRefundable', 'totalPaid', 'refundCost'], CheckIsNumberValidator::class, 'allowInt' => true],

            ['tickets', IsArrayValidator::class, 'skipOnEmpty' => false],

            ['auxiliaryOptions', IsArrayValidator::class, 'skipOnEmpty' => true],
        ];
    }

    public function formName(): string
    {
        return 'refund';
    }

    public function load($data, $formName = null)
    {
        if (!empty($data['refund']['tickets'])) {
            foreach ($data['refund']['tickets'] as $ticket) {
                $ticketForm = new TicketForm();
                $ticketForm->load($ticket);
                $this->ticketForms[] = $ticketForm;
            }
        }
        if (!empty($data['refund']['auxiliaryOptions'])) {
            foreach ($data['refund']['auxiliaryOptions'] as $auxiliaryOption) {
                $auxiliaryOptionForm = new AuxiliaryOptionForm();
                $auxiliaryOptionForm->load($auxiliaryOption);
                $this->auxiliaryOptionsForms[] = $auxiliaryOptionForm;
            }
        }
        return parent::load($data, $formName); // TODO: Change the autogenerated stub
    }

    /**
     * @return TicketForm[]
     */
    public function getTicketForms(): array
    {
        return $this->ticketForms;
    }

    public function setTicketForm(TicketForm $form): void
    {
        $this->ticketForms[] = $form;
    }

    /**
     * @return AuxiliaryOptionForm[]
     */
    public function getAuxiliaryOptionsForms(): array
    {
        return $this->auxiliaryOptionsForms;
    }
}
