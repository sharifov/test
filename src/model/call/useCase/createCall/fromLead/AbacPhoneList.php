<?php

namespace src\model\call\useCase\createCall\fromLead;

use common\models\Employee;
use common\models\Lead;
use modules\lead\src\abac\dto\LeadCommunicationAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use Yii;

/**
 * Class AbacPhoneList
 *
 * @property PhoneFrom[] $list
 * @property PhoneFrom[] $fromPhones
 * @property Employee $user
 * @property Lead $lead
 * @property bool $canCreateCall
 */
class AbacPhoneList
{
    private ?array $list = null;
    private ?array $fromPhones = null;
    private Employee $user;
    private Lead $lead;
    private ?bool $canCreateCall = null;

    public function __construct(Employee $user, Lead $lead)
    {
        $this->user = $user;
        $this->lead = $lead;
    }

    /**
     * @return PhoneFrom[]
     */
    public function getList(): array
    {
        if ($this->list !== null) {
            return $this->list;
        }

        $this->list = [];

        if ($this->canMakeCall()) {
            foreach ($this->getFromPhones() as $fromPhone) {
                $tempAbacDto = new LeadCommunicationAbacDto($this->lead, [$fromPhone], $this->user->id);
                if (Yii::$app->abac->can($tempAbacDto, LeadAbacObject::OBJ_LEAD_COMMUNICATION, LeadAbacObject::ACTION_MAKE_CALL, $this->user)) {
                    $this->list[] = $fromPhone;
                }
                unset($tempAbacDto);
            }
        }

        return $this->list;
    }

    public function isExist(string $number): bool
    {
        foreach ($this->getList() as $phone) {
            if ($phone->isEqual($number)) {
                return true;
            }
        }
        return false;
    }

    public function canMakeCall(): bool
    {
        if ($this->canCreateCall !== null) {
            return $this->canCreateCall;
        }

        $leadCommunicationAbacDto = new LeadCommunicationAbacDto($this->lead, $this->getFromPhones(), $this->user->id);
        $this->canCreateCall = Yii::$app->abac->can($leadCommunicationAbacDto, LeadAbacObject::OBJ_LEAD_COMMUNICATION, LeadAbacObject::ACTION_MAKE_CALL, $this->user);

        return $this->canCreateCall;
    }

    public function format(): array
    {
        return array_map(fn (PhoneFrom $phone) => $phone->format(), $this->getList());
    }

    private function getFromPhones(): array
    {
        if ($this->fromPhones !== null) {
            return $this->fromPhones;
        }

        $this->fromPhones = (new PhoneFromList($this->user->id, $this->lead->project_id, $this->lead->l_dep_id))->getList();

        return $this->fromPhones;
    }
}
