<?php

namespace modules\flight\src\useCases\reprotectionCreate\form;

use common\components\validators\CheckJsonValidator;
use common\models\Project;
use frontend\helpers\JsonHelper;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\reprotectionCreate\form\flightQuote\FlightQuoteForm;
use src\helpers\ErrorsToStringHelper;
use yii\base\Model;

/**
 * Class ReprotectionCreateForm
 *
 * @property $booking_id
 * @property $base_booking_id
 * @property $is_automate
 * @property $flight_quote
 * @property $project_key
 * @property $refundAllowed
 *
 * @property Project|null $project
 * @property FlightQuoteForm|null $flightQuoteForm
 */
class ReprotectionCreateForm extends Model
{
    public $booking_id;
    public $base_booking_id;
    public $is_automate;
    public $flight_quote;
    public $project_key;
    public $refundAllowed;

    private ?Project $project;
    private ?FlightQuoteForm $flightQuoteForm;

    public function rules(): array
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id'], 'string', 'max' => 10],

            [['base_booking_id'], 'string', 'max' => 10],
            ['base_booking_id', 'filter', 'filter' => function ($value) {
                return !empty($value) ? $value : $this->booking_id;
            }],

            [['project_key'], 'required'],
            [['project_key'], 'string', 'max' => 50],
            [['project_key'], 'detectProject'],

            [['is_automate'], 'boolean', 'strict' => true, 'trueValue' => true, 'falseValue' => false, 'skipOnEmpty' => true],
            [['is_automate'], 'default', 'value' => false],

            [['refundAllowed'], 'boolean', 'strict' => true, 'trueValue' => true, 'falseValue' => false, 'skipOnEmpty' => true],
            [['refundAllowed'], 'default', 'value' => true],

            [['flight_quote'], CheckJsonValidator::class, 'skipOnEmpty' => true],
            [['flight_quote'], 'checkFlightQuoteForm'],

            //[['booking_id'], 'checkExistByHash'],
        ];
    }

    public function checkFlightQuoteForm($attribute)
    {
        if (!empty($this->flight_quote)) {
            $flightQuoteForm = new FlightQuoteForm();
            if (!$flightQuoteForm->load($this->flight_quote)) {
                $this->addError($attribute, 'FlightQuoteForm not loaded');
            } elseif (!$flightQuoteForm->validate()) {
                $this->addError($attribute, 'FlightQuoteForm: ' . ErrorsToStringHelper::extractFromModel($flightQuoteForm, ' '));
            } else {
                $this->flightQuoteForm = $flightQuoteForm;
            }
        }
    }

    public function checkExistByHash($attribute)
    {
        $hash = FlightRequest::generateHashFromDataJson($this->getAttributes());
        if (FlightRequest::findOne(['fr_hash' => $hash])) {
            $this->addError($attribute, 'Flight Request already exist. Hash (' . $hash . ')');
        }
    }

    public function detectProject($attribute)
    {
        if ($project = Project::findOne(['project_key' => $this->project_key])) {
            $this->project = $project;
        } else {
            $this->addError($attribute, 'Project not found (' . $this->project_key . ')');
        }
    }

    public function formName(): string
    {
        return '';
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function getFlightQuoteForm(): ?FlightQuoteForm
    {
        return $this->flightQuoteForm;
    }
}
