<?php

namespace src\model\call\useCase\createCall\fromLead;

use common\models\Employee;
use common\models\Lead;
use modules\lead\src\abac\communicationBlock\LeadCommunicationBlockAbacObject;
use modules\lead\src\abac\dto\LeadCommunicationBlockAbacDto;
use Yii;

/**
 * Class AbacCallFromNumberList
 *
 * @property CallFromNumber[]|null $list
 * @property CallFromNumber[]|null $callFromNumbers
 * @property Employee $user
 * @property Lead $lead
 * @property bool $canCreateCall
 */
class AbacCallFromNumberList
{
    private ?array $list = null;
    private ?array $callFromNumbers = null;
    private Employee $user;
    private Lead $lead;
    private ?bool $canCreateCall = null;

    public function __construct(Employee $user, Lead $lead)
    {
        $this->user = $user;
        $this->lead = $lead;
    }

    /**
     * @return CallFromNumber[]
     */
    public function getList(): array
    {
        if ($this->list !== null) {
            return $this->list;
        }

        $this->list = [];

        if ($this->canMakeCall()) {
            foreach ($this->getCallFromNumbers() as $fromPhone) {
                $tempAbacDto = new LeadCommunicationBlockAbacDto($this->lead, [$fromPhone], [], [], $this->user->id);
                if (Yii::$app->abac->can($tempAbacDto, LeadCommunicationBlockAbacObject::NS, LeadCommunicationBlockAbacObject::ACTION_MAKE_CALL, $this->user)) {
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

        $leadCommunicationBlockAbacDto = new LeadCommunicationBlockAbacDto($this->lead, $this->getCallFromNumbers(), [], [], $this->user->id);
        $this->canCreateCall = Yii::$app->abac->can($leadCommunicationBlockAbacDto, LeadCommunicationBlockAbacObject::NS, LeadCommunicationBlockAbacObject::ACTION_MAKE_CALL, $this->user);

        return $this->canCreateCall;
    }

    public function format(): array
    {
        return array_map(static fn (CallFromNumber $phone) => $phone->format(), $this->getList());
    }

    private function getCallFromNumbers(): array
    {
        if ($this->callFromNumbers !== null) {
            return $this->callFromNumbers;
        }

        $this->callFromNumbers = (new CallFromNumberList($this->user->id, $this->lead->project_id, $this->lead->l_dep_id))->getList();

        return $this->callFromNumbers;
    }
}
