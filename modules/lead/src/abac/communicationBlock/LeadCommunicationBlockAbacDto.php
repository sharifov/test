<?php

namespace modules\lead\src\abac\communicationBlock;

use common\models\Lead;

/**
 * @property bool $is_owner
 * @property bool $has_owner
 * @property string $status_name
 * @property string $project_name
 * @property string $department_name
 * @property bool $client_is_excluded
 * @property bool $call_from_personal
 * @property bool $call_from_general
 * @property bool $sms_from_personal
 * @property bool $sms_from_general
 * @property bool $email_from_personal
 * @property bool $email_from_general
 */
class LeadCommunicationBlockAbacDto extends \stdClass
{
    public bool $is_owner;
    public bool $has_owner;
    public string $status_name;
    public string $project_name;
    public string $department_name;
    public string $client_is_excluded;
    public bool $call_from_personal;
    public bool $call_from_general;
    public bool $sms_from_personal;
    public bool $sms_from_general;
    public bool $email_from_personal;
    public bool $email_from_general;

    /**
     * @param Lead $lead
     * @param \src\model\phoneList\services\AvailablePhoneNumber[] $callFromNumbers
     * @param \src\model\phoneList\services\AvailablePhoneNumber[] $smsFromNumbers
     * @param \src\model\emailList\services\AvailableEmail[] $emailFromEmails
     * @param int $userId
     */
    public function __construct(Lead $lead, array $callFromNumbers, array $smsFromNumbers, array $emailFromEmails, int $userId)
    {
        $this->is_owner = $lead->isOwner($userId);
        $this->has_owner = $lead->hasOwner();
        $this->status_name = Lead::STATUS_LIST[$lead->status] ?? '';
        $this->project_name = $lead->project->name ?? '';
        $this->department_name = $lead->lDep->dep_name ?? '';
        $this->client_is_excluded = (bool)$lead->client->cl_excluded;
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
