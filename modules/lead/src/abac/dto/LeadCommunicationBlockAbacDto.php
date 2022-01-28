<?php

namespace modules\lead\src\abac\dto;

use common\models\Lead;
use src\model\call\useCase\createCall\fromLead\PhoneFrom;

/**
 * @property bool $is_owner
 * @property bool $has_owner
 * @property string $status_name
 * @property string $project_name
 * @property string $department_name
 * @property bool $client_is_excluded
 * @property bool $phone_from_personal
 * @property bool $phone_from_general
 */
class LeadCommunicationBlockAbacDto extends \stdClass
{
    public bool $is_owner;
    public bool $has_owner;
    public string $status_name;
    public string $project_name;
    public string $department_name;
    public string $client_is_excluded;
    public bool $phone_from_personal;
    public bool $phone_from_general;

    /**
     * @param Lead $lead
     * @param PhoneFrom[] $phones
     * @param int $userId
     */
    public function __construct(Lead $lead, array $phones, int $userId)
    {
        $this->is_owner = $lead->isOwner($userId);
        $this->has_owner = $lead->hasOwner();
        $this->status_name = Lead::STATUS_LIST[$lead->status] ?? '';
        $this->project_name = $lead->project->name ?? '';
        $this->department_name = $lead->lDep->dep_name ?? '';
        $this->client_is_excluded = (bool)$lead->client->cl_excluded;
        if ($phones) {
            $this->phone_from_personal = !empty(array_filter($phones, fn(PhoneFrom $phone) => $phone->isPersonalType()));
            $this->phone_from_general = !empty(array_filter($phones, fn(PhoneFrom $phone) => $phone->isGeneralType()));
        } else {
            $this->phone_from_personal = false;
            $this->phone_from_general = false;
        }
    }
}
