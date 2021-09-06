<?php

namespace modules\flight\src\useCases\reprotectionExchange\form;

use modules\flight\src\useCases\reprotectionExchange\service\ReProtectionExchangeService;
use sales\entities\cases\Cases;
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

            [['flight_request'], 'string', 'max' => 200],
        ];
    }

    public function detectCase($attribute): void
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
