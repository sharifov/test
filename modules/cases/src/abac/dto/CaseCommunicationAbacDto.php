<?php

namespace modules\cases\src\abac\dto;

use src\entities\cases\Cases;
use src\entities\cases\CasesStatus;
use src\model\call\useCase\createCall\fromCase\PhoneFrom;

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
class CaseCommunicationAbacDto extends \stdClass
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
     * @param Cases $case
     * @param PhoneFrom[] $phones
     * @param int $userId
     */
    public function __construct(Cases $case, array $phones, int $userId)
    {
        $this->is_owner = $case->isOwner($userId);
        $this->has_owner = $case->hasOwner();
        $this->status_name = CasesStatus::STATUS_LIST[$case->cs_status] ?? '';
        $this->project_name = $case->project->name ?? '';
        $this->department_name = $case->department->dep_name ?? '';
        $this->client_is_excluded = (bool)$case->client->cl_excluded;
        if ($phones) {
            $this->phone_from_personal = !empty(array_filter($phones, fn(PhoneFrom $phone) => $phone->isPersonalType()));
            $this->phone_from_general = !empty(array_filter($phones, fn(PhoneFrom $phone) => $phone->isGeneralType()));
        } else {
            $this->phone_from_personal = false;
            $this->phone_from_general = false;
        }
    }
}
