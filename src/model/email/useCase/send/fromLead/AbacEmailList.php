<?php

namespace src\model\email\useCase\send\fromLead;

use common\models\Employee;
use common\models\Lead;
use modules\lead\src\abac\communicationBlock\LeadCommunicationBlockAbacObject;
use modules\lead\src\abac\communicationBlock\LeadCommunicationBlockAbacDto;
use src\model\emailList\services\AvailableEmail;
use src\model\emailList\services\AvailableEmailList;
use Yii;

/**
 * Class AbacEmailList
 *
 * @property AvailableEmail[]|null $list
 * @property AvailableEmail[]|null $emailFromEmails
 * @property Employee $user
 * @property Lead $lead
 */
class AbacEmailList
{
    private ?array $list = null;
    private ?array $emailFromEmails = null;
    private Employee $user;
    private Lead $lead;

    public function __construct(Employee $user, Lead $lead)
    {
        $this->user = $user;
        $this->lead = $lead;
    }

    /**
     * @return AvailableEmail[]
     */
    public function getList(): array
    {
        if ($this->list !== null) {
            return $this->list;
        }

        $this->list = [];

        foreach ($this->getEmailFromEmails() as $email) {
            $tempAbacDto = new LeadCommunicationBlockAbacDto($this->lead, [], [], [$email], $this->user->id);
            /** @abac $tempAbacDto, LeadCommunicationBlockAbacObject::NS, LeadCommunicationBlockAbacObject::ACTION_SEND_EMAIL, Validate Email From for send email from Lead View page */
            if (Yii::$app->abac->can($tempAbacDto, LeadCommunicationBlockAbacObject::NS, LeadCommunicationBlockAbacObject::ACTION_SEND_EMAIL, $this->user)) {
                $this->list[] = $email;
            }
            unset($tempAbacDto);
        }

        return $this->list;
    }

    public function isExist(string $value): bool
    {
        foreach ($this->getList() as $email) {
            if ($email->isEqual($value)) {
                return true;
            }
        }
        return false;
    }

    public function canSendEmail(): bool
    {
        return !empty($this->getList());
    }

    public function format(): array
    {
        $list = [];
        foreach ($this->getList() as $email) {
            $list[$email->email] = $email->format();
        }
        return $list;
    }

    public function first(): ?string
    {
        $list = $this->getList();
        if (!$list) {
            return null;
        }
        return $list[0]->email;
    }

    private function getEmailFromEmails(): array
    {
        if ($this->emailFromEmails !== null) {
            return $this->emailFromEmails;
        }

        $departmentParams = $this->lead->l_dep_id ? $this->lead->lDep->getParams() : null;

        $emails = new AvailableEmailList(
            $this->user->id,
            $this->lead->project_id,
            $this->lead->l_dep_id,
            $departmentParams ? $departmentParams->object->lead->emailDefaultType->isGeneral() : false
        );

        $this->emailFromEmails = $emails->getList();

        return $this->emailFromEmails;
    }
}
