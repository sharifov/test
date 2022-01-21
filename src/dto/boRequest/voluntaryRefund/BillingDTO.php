<?php

namespace src\dto\boRequest\voluntaryRefund;

use modules\flight\src\useCases\api\voluntaryRefundCreate\VoluntaryRefundCreateForm;
use src\helpers\CountryHelper;
use webapi\src\forms\billing\BillingInfoForm;

/**
 * Class BillingDTO
 * @package src\dto\boRequest\voluntaryRefund
 *
 * @property string $address
 * @property string $countryCode
 * @property string $country
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $phone
 * @property string $email
 */
class BillingDTO
{
    public string $address = '';
    public string $countryCode = '';
    public string $country = '';
    public string $city = '';
    public string $state = '';
    public string $zip = '';
    public string $phone = '';
    public string $email = '';

    public function fillInByBillingInfoForm(BillingInfoForm $form): BillingDTO
    {
        $this->address = $form->address_line1;
        $this->countryCode = $form->country_id;
        $this->country = $form->country;
        $this->city = $form->city;
        $this->state = $form->state;
        $this->zip = $form->zip;
        $this->phone = $form->contact_phone;
        $this->email = $form->contact_email;
        return $this;
    }
}
