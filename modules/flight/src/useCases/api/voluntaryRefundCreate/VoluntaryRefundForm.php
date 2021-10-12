<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

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
 *
 * @property TicketForm[] $ticketForms
 */
class VoluntaryRefundForm extends Model
{
    public $currency;
    public $tickets;

    /**
     * @var TicketForm[] $ticketForms
     */
    public array $ticketForms = [];

    public function rules(): array
    {
        return [
            ['currency', 'required'],
            ['currency', 'string', 'max' => 3],
            ['currency', 'exist', 'targetClass' => Currency::class, 'targetAttribute' => 'cur_code'],

            ['tickets', IsArrayValidator::class, 'skipOnEmpty' => false],
            ['tickets', 'ticketsValidation', 'skipOnEmpty' => false]
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
                $this->addError('sss' . '.' . $key, 'Refund.tickets.' . $key . ': ' . ErrorsToStringHelper::extractFromModel($ticketForm, ', '));
                return false;
            }

            $this->ticketForms[] = $ticketForm;
        }

        return true;
    }
}
