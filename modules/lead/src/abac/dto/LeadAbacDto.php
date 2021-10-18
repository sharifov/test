<?php

namespace modules\lead\src\abac\dto;

use common\models\Lead;
use common\models\Employee;
use common\models\Quote;
use sales\access\EmployeeGroupAccess;
use yii\helpers\VarDumper;

/**
 * @property bool $is_owner
 * @property bool $has_owner
 * @property bool $has_owner_query
 * @property bool $is_common_group
 * @property int|null $status_id
 */
class LeadAbacDto extends \stdClass
{
    public bool $is_owner;
    public bool $has_owner;
    public bool $has_owner_query;
    public bool $is_common_group = false;
    public ?int $status_id = null;
    public bool $is_lead_shift_time;
    public bool $can_take_new_lead;
    public bool $has_applied_quote;

    /**
     * @param Lead|null $lead
     * @param int $userId
     */
    public function __construct(?Lead $lead, int $userId)
    {
        if ($lead) {
            $this->is_owner = $lead->isOwner($userId);
            $this->has_owner = $lead->hasOwner();
            $this->has_owner_query = $this->has_owner;

            if ($this->has_owner) {
                $this->is_common_group = EmployeeGroupAccess::isUserInCommonGroup($userId, $lead->employee_id);
            }

            $user = Employee::findOne(['id' => $userId]);
            if ($user) {
                $this->has_applied_quote = $lead->hasAppliedQuote();
                $this->is_lead_shift_time = $user->checkShiftTime();
                $this->can_take_new_lead = $user->accessTakeNewLead();  # может к этой строке добавить и закомментированную логику ниже ??
//                private function checkTakeAccess(Lead $lead, Employee $user): void
//                {
    //                if ($user->isAgent() && ($lead->isPending() || $lead->isBookFailed())) {
    //                    if ($lead->isPending()) {
    //                        $this->takeGuard->minPercentGuard($user);
    //                    }
    //                    $fromStatuses = [];
    //                    if ($lead->isBookFailed()) {
    //                        $fromStatuses = [Lead::STATUS_BOOK_FAILED];
    //                    }
    //                    $this->takeGuard->frequencyMinutesGuard($user, [], $fromStatuses);
    //                }
    //            }
            }

            $this->status_id = $lead->status;
        }
    }
}
