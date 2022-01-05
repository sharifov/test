<?php

namespace modules\email\src\abac\dto;

use common\models\Email;
use sales\auth\Auth;
use common\models\UserProjectParams;
use sales\access\EmployeeGroupAccess;

class EmailAbacDto extends \stdClass
{
    public bool $is_email_owner;
    public bool $has_creator;
    public bool $is_case_owner = false;
    public bool $is_lead_owner = false;
    public bool $is_address_owner = false;
    public bool $is_common_group = false;

    public function __construct(?Email $email)
    {
        if ($email) {
            $this->is_email_owner = $email->isCreatedUser(Auth::id());
            $this->has_creator = $email->hasCreatedUser();
            if ($email->eCase) {
                $this->is_case_owner = $email->eCase->isOwner(Auth::id());
            }

            if ($email->eLead) {
                $this->is_lead_owner = $email->eLead->isOwner(Auth::id());
            }

            $this->is_address_owner = self::isUserEmailAddressOwner($email);

            if ($email->hasCreatedUser()) {
                $this->is_common_group = EmployeeGroupAccess::isUserInCommonGroup(Auth::id(), $email->e_created_user_id);
            }
        }
    }

    private static function isUserEmailAddressOwner(Email $email): bool
    {
        return UserProjectParams::find()
            ->byUserId(Auth::id())
            ->byEmail([$email->e_email_from, $email->e_email_to], false)
            ->exists();
    }
}
