<?php

namespace modules\lead\src\abac\dto;

use common\models\Lead;
use common\models\Employee;
use common\models\query\LeadQuery;
use src\access\EmployeeGroupAccess;
use src\access\EmployeeDepartmentAccess;
use src\access\EmployeeProjectAccess;
use yii\helpers\VarDumper;

/**
 * @property bool $is_owner
 * @property bool $has_owner
 * @property bool $has_owner_query
 * @property bool $is_common_group
 * @property bool $isShiftTime
 * @property bool $withinPersonalTakeLimits
 * @property bool $hasAppliedQuote
 * @property bool $isInProject
 * @property bool $isInDepartment
 * @property bool $canTakeByFrequencyMinutes
 * @property int|null $status_id
 * @property string $formAttribute
 * @property int $snoozeCount
 * @property string|null $closeReason
 */
class LeadAbacDto extends \stdClass
{
    public bool $is_owner;
    public bool $has_owner;
    public bool $has_owner_query;
    public bool $is_common_group = false;
    public ?int $status_id = null;
    public bool $isShiftTime;
    public bool $withinPersonalTakeLimits;
    public bool $hasAppliedQuote;
    public bool $isInProject = false;
    public bool $isInDepartment = false;
    public bool $canTakeByFrequencyMinutes;
    public string $formAttribute = '';
    public ?bool $isNewRecord = null;
    public array $formMultiAttribute = [];
    public int $snoozeCount = 10;
    public ?string $closeReason = '';
    public ?int $quotesCount = null;
    public ?int $flightSegmentsCount = null;

    /**
     * @param Lead|null $lead
     * @param int|null $userId
     * @throws \Exception
     */
    public function __construct(?Lead $lead, ?int $userId)
    {
        if ($lead) {
            $this->is_owner = $lead->isOwner($userId);
            $this->has_owner = $lead->hasOwner();
            $this->has_owner_query = $this->has_owner;
            $this->quotesCount = $lead->quotesCount;
            $this->flightSegmentsCount = $lead->leadFlightSegmentsCount;

            if ($this->has_owner) {
                $this->is_common_group = EmployeeGroupAccess::isUserInCommonGroup($userId, $lead->employee_id);
            }

            if ($lead->project_id) {
                $this->isInProject = EmployeeProjectAccess::isInProject($lead->project_id, $userId);
            }

            if ($lead->l_dep_id) {
                $this->isInDepartment = EmployeeDepartmentAccess::isInDepartment($lead->l_dep_id, $userId);
            }

            $user = Employee::findOne(['id' => $userId]);
            if ($user) {
                $this->hasAppliedQuote = $lead->hasAppliedQuote();
                $this->isShiftTime = $user->checkShiftTime();
                $this->withinPersonalTakeLimits = $user->accessTakeNewLead();

                $fromStatuses = [];
                if ($lead->isBookFailed()) {
                    $fromStatuses = [Lead::STATUS_BOOK_FAILED];
                }
                $isAccessLeadByFrequency = $user->accessTakeLeadByFrequencyMinutes([], $fromStatuses);
                $this->canTakeByFrequencyMinutes = $isAccessLeadByFrequency['access'];
            }

            $this->status_id = $lead->status;
            if ($userId) {
                $this->snoozeCount = LeadQuery::countSnoozeLeadsByOwner($userId);
            }
        }
    }
}
