<?php

namespace modules\flight\src\useCases\reprotectionExchange\form;

use frontend\helpers\JsonHelper;
use modules\flight\src\useCases\reprotectionExchange\service\ReProtectionExchangeService;
use sales\entities\cases\Cases;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\validators\EmailValidator;
use borales\extensions\phoneInput\PhoneInputValidator;

/**
 * Class ReProtectionExchangeForm
 *
 * @property $booking_id
 * @property $email
 * @property $phone
 * @property $flight_request
 *
 * @property array $warnings
 * @property bool $isEmailValid
 * @property bool $isPhoneValid
 * @property Cases|null $case
 */
class ReProtectionExchangeForm extends \yii\base\Model
{
    public $booking_id;
    public $email;
    public $phone;
    public $flight_request;

    private bool $isEmailValid = false;
    private bool $isPhoneValid = false;
    private array $warnings = [];
    private ?Cases $case = null;

    public function rules(): array
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id'], 'string', 'min' => 7, 'max' => 10],
            [['booking_id'], 'detectCase'],

            [['email'], 'string', 'max' => 100],
            [['email'], 'checkEmail'],

            [['phone'], 'string', 'max' => 20],
            [['phone'], 'checkPhone'],

            [['flight_request'], 'prepareFlightRequest'],
        ];
    }

    public function prepareFlightRequest(string $attribute): void
    {
        if (!empty($this->flight_request)) {
            $result = null;
            if (is_string($this->flight_request)) {
                if (JsonHelper::isValidJson($this->flight_request)) {
                    $this->flight_request = JsonHelper::decode($this->flight_request);
                } else {
                    $result = $this->flight_request;
                }
            }
            if (is_array($this->flight_request)) {
                $result = $this->flightRequestToString($this->flight_request);
            }
            if (empty($result)) {
                \Yii::warning(
                    [
                        'message' => 'FlightRequest converting to string failed',
                        'data' => VarDumper::dumpAsString($this->flight_request)
                     ],
                    'ReProtectionExchangeForm:prepareFlightRequest'
                );
                $this->warnings[] = 'FlightRequest converting to case note failed';
            } else {
                $this->flight_request = $result;
            }
        }
    }

    private function flightRequestToString(array $flightRequest, string $delimiter = ' : ', string $eol = PHP_EOL): string
    {
        $result = '';
        foreach ($flightRequest as $key => $value) {
            if (is_array($value)) {
                return $this->flightRequestToString($value);
            }
            $result .= $key . $delimiter . $value . $eol;
        }
        return $result;
    }

    public function detectCase(string $attribute): void
    {
        if (!$this->case = ReProtectionExchangeService::getCaseByBookingId($this->booking_id)) {
            $this->addError($attribute, 'Case with ProductQuoteChange not found');
        }
    }

    public function checkEmail(): void
    {
        if (!empty($this->email)) {
            $emailValidator = new EmailValidator();
            if ($emailValidator->validate($this->email)) {
                $this->isEmailValid = true;
            } else {
                $this->warnings[] = 'Email validation failed';
            }
        }
    }

    public function checkPhone(): void
    {
        if (!empty($this->phone)) {
            $phoneValidator = new PhoneInputValidator();
            if ($phoneValidator->validate($this->phone)) {
                $this->isPhoneValid = true;
            } else {
                $this->warnings[] = 'Phone validation failed';
            }
        }
    }

    public function formName(): string
    {
        return '';
    }

    public function isEmailValid(): bool
    {
        return $this->isEmailValid;
    }

    public function isPhoneValid(): bool
    {
        return $this->isPhoneValid;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function getCase(): ?Cases
    {
        return $this->case;
    }
}
