<?php

namespace sales\model\clientAccount;

use sales\model\clientAccount\entity\ClientAccount;
use sales\model\clientAccount\form\ClientAccountUpdateApiForm;
use sales\repositories\Repository;

/**
 * Class ClientAccountRepository
 */
class ClientAccountRepository extends Repository
{
    public function save(ClientAccount $clientAccount, bool $runValidation = false): ClientAccount
    {
        if (!$clientAccount->save($runValidation)) {
            throw new \RuntimeException('ClientAccount saving failed');
        }
        return $clientAccount;
    }

    public function fillForUpdate(ClientAccount $clientAccount, ClientAccountUpdateApiForm $form): ClientAccount
    {
        $clientAccount->ca_project_id = $form->project_id;
        $clientAccount->ca_hid = $form->hid;
        $clientAccount->ca_email = $form->email;
        $clientAccount->ca_username = $form->username;
        $clientAccount->ca_first_name = $form->first_name;
        $clientAccount->ca_middle_name = $form->middle_name;
        $clientAccount->ca_last_name = $form->last_name;
        $clientAccount->ca_nationality_country_code = $form->nationality_country_code;
        $clientAccount->ca_dob = $form->dob;
        $clientAccount->ca_gender = $form->gender;
        $clientAccount->ca_phone = $form->phone;
        $clientAccount->ca_subscription = $form->subscription;
        $clientAccount->ca_language_id = $form->language_id;
        $clientAccount->ca_currency_code = $form->currency_code;
        $clientAccount->ca_timezone = $form->timezone;
        $clientAccount->ca_created_ip = $form->created_ip;
        $clientAccount->ca_enabled = $form->enabled;
        $clientAccount->ca_origin_created_dt = $form->origin_created_dt;
        $clientAccount->ca_origin_updated_dt = $form->origin_updated_dt;

        return $clientAccount;
    }
}
