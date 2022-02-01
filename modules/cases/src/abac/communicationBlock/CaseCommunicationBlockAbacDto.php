<?php

namespace modules\cases\src\abac\communicationBlock;

use src\entities\cases\Cases;
use src\entities\cases\CasesStatus;

/**
 * @property bool $is_owner
 * @property bool $has_owner
 * @property string $status_name
 * @property string $project_name
 * @property bool $project_sms_enable
 * @property string $department_name
 * @property bool $client_is_excluded
 * @property bool $call_from_personal
 * @property bool $call_from_general
 * @property bool $sms_from_personal
 * @property bool $sms_from_general
 * @property bool $email_from_personal
 * @property bool $email_from_general
 */
class CaseCommunicationBlockAbacDto extends \stdClass
{
    public bool $is_owner;
    public bool $has_owner;
    public string $status_name;
    public string $project_name;
    public bool $project_sms_enable;
    public string $department_name;
    public string $client_is_excluded;
    public bool $call_from_personal;
    public bool $call_from_general;
    public bool $sms_from_personal;
    public bool $sms_from_general;
    public bool $email_from_personal;
    public bool $email_from_general;

    /**
     * @param Cases $case
     * @param \src\model\phoneList\services\AvailablePhoneNumber[] $callFromNumbers
     * @param \src\model\phoneList\services\AvailablePhoneNumber[] $smsFromNumbers
     * @param \src\model\emailList\services\AvailableEmail[] $emailFromEmails
     * @param int $userId
     */
    public function __construct(Cases $case, array $callFromNumbers, array $smsFromNumbers, array $emailFromEmails, int $userId)
    {
        $this->is_owner = $case->isOwner($userId);
        $this->has_owner = $case->hasOwner();
        $this->status_name = CasesStatus::STATUS_LIST[$case->cs_status] ?? '';
        $this->project_name = $case->project->name ?? '';
        if ($case->project->getParams()->sms->isEnabled()) {
            $this->project_sms_enable = true;
        } else {
            $this->project_sms_enable = false;
        }
        $this->department_name = $case->department->dep_name ?? '';
        $this->client_is_excluded = (bool)$case->client->cl_excluded;
        if ($callFromNumbers) {
            $this->call_from_personal = !empty(array_filter($callFromNumbers, static fn($number) => $number->isPersonalType()));
            $this->call_from_general = !empty(array_filter($callFromNumbers, static fn($number) => $number->isGeneralType()));
        } else {
            $this->call_from_personal = false;
            $this->call_from_general = false;
        }
        if ($smsFromNumbers) {
            $this->sms_from_personal = !empty(array_filter($smsFromNumbers, static fn($number) => $number->isPersonalType()));
            $this->sms_from_general = !empty(array_filter($smsFromNumbers, static fn($number) => $number->isGeneralType()));
        } else {
            $this->sms_from_personal = false;
            $this->sms_from_general = false;
        }
        if ($emailFromEmails) {
            $this->email_from_personal = !empty(array_filter($emailFromEmails, static fn($email) => $email->isPersonalType()));
            $this->email_from_general = !empty(array_filter($emailFromEmails, static fn($email) => $email->isGeneralType()));
        } else {
            $this->email_from_personal = false;
            $this->email_from_general = false;
        }
    }
}
