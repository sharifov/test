<?php

namespace sales\dto\billingInfo;

use webapi\src\forms\billing\BillingInfoForm;

/**
 * Class BillingInfoDTO
 */
class BillingInfoDTO
{
    public $first_name;
    public $last_name;
    public $middle_name;
    public $address_line1;
    public $address_line2;
    public $country_id;
    public $city;
    public $state;
    public $zip;
    public $company_name;
    public $contact_phone;
    public $contact_email;
    public $contact_name;
    public $orderId;
    public $creditCardId;
    public $paymentMethodId;

    public function fillByVoluntaryForm(
        BillingInfoForm $billingInfoForm,
        int $orderId,
        ?int $creditCardId,
        ?int $paymentMethodId
    ): BillingInfoDTO {
        $this->first_name = $billingInfoForm->first_name;
        $this->last_name = $billingInfoForm->last_name;
        $this->middle_name = $billingInfoForm->middle_name;
        $this->address_line1 = $billingInfoForm->address_line1;
        $this->address_line2 = $billingInfoForm->address_line2;
        $this->country_id = $billingInfoForm->country_id;
        $this->city = $billingInfoForm->city;
        $this->state = $billingInfoForm->state;
        $this->zip = $billingInfoForm->zip;
        $this->company_name = $billingInfoForm->company_name;
        $this->contact_phone = $billingInfoForm->contact_phone;
        $this->contact_email = $billingInfoForm->contact_email;
        $this->contact_name = $billingInfoForm->contact_name;
        $this->creditCardId = $creditCardId;
        $this->orderId = $orderId;
        $this->paymentMethodId = $paymentMethodId;
        return $this;
    }
}
