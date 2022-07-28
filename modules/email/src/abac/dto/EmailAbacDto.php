<?php

namespace modules\email\src\abac\dto;

use src\auth\Auth;
use common\models\UserProjectParams;
use src\access\EmployeeGroupAccess;
use src\entities\email\EmailInterface;

class EmailAbacDto extends \stdClass
{
    public bool $is_email_owner;
    public bool $has_creator;
    public bool $is_case_owner = false;
    public bool $is_lead_owner = false;
    public bool $is_address_owner = false;
    public bool $is_common_group = false;

    public function __construct(EmailInterface $email)
    {
        if ($email) {
            $this->is_email_owner = $email->isCreatedUser(Auth::id());
            $this->has_creator = $email->hasCreatedUser();
            if ($email->case !== null) {
                $this->is_case_owner = $email->case->isOwner(Auth::id());
            }

            if ($email->lead !== null) {
                $this->is_lead_owner = $email->lead->isOwner(Auth::id());
            }

            $this->is_address_owner = self::isUserEmailAddressOwner($email->getEmailFrom(false), $email->getEmailTo(false));

            if ($email->hasCreatedUser()) {
                $this->is_common_group = EmployeeGroupAccess::isUserInCommonGroup(Auth::id(), $email->e_created_user_id);
            }
        }
    }

    private static function isUserEmailAddressOwner(string $emailFrom, string $emailTo): bool
    {
        return UserProjectParams::find()
            ->byUserId(Auth::id())
            ->byEmail([$emailFrom, $emailTo], false)
            ->exists();
    }
}
