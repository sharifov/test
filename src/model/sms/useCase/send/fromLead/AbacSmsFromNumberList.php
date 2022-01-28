<?php

namespace src\model\sms\useCase\send\fromLead;

use common\models\Employee;
use common\models\Lead;
use modules\lead\src\abac\communicationBlock\LeadCommunicationBlockAbacObject;
use modules\lead\src\abac\dto\LeadCommunicationBlockAbacDto;
use Yii;

/**
 * Class AbacSmsFromNumberList
 *
 * @property SmsFromNumber[]|null $list
 * @property SmsFromNumber[]|null $fromNumbers
 * @property Employee $user
 * @property Lead $lead
 * @property bool|null $canSendSmsFlag
 */
class AbacSmsFromNumberList
{
    private ?array $list = null;
    private ?array $fromNumbers = null;
    private Employee $user;
    private Lead $lead;
    private ?bool $canSendSmsFlag = null;

    public function __construct(Employee $user, Lead $lead)
    {
        $this->user = $user;
        $this->lead = $lead;
    }

    /**
     * @return SmsFromNumber[]
     */
    public function getList(): array
    {
        if ($this->list !== null) {
            return $this->list;
        }

        $this->list = [];

        if ($this->canSendSms()) {
            foreach ($this->getSmsFromNumbers() as $fromPhone) {
                $tempAbacDto = new LeadCommunicationBlockAbacDto($this->lead, [], [$fromPhone], [], $this->user->id);
                if (Yii::$app->abac->can($tempAbacDto, LeadCommunicationBlockAbacObject::NS, LeadCommunicationBlockAbacObject::ACTION_SEND_SMS, $this->user)) {
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

    public function canSendSms(): bool
    {
        if ($this->canSendSmsFlag !== null) {
            return $this->canSendSmsFlag;
        }

        $leadCommunicationBlockAbacDto = new LeadCommunicationBlockAbacDto($this->lead, [], $this->getSmsFromNumbers(), [], $this->user->id);
        $this->canSendSmsFlag = Yii::$app->abac->can($leadCommunicationBlockAbacDto, LeadCommunicationBlockAbacObject::NS, LeadCommunicationBlockAbacObject::ACTION_SEND_SMS, $this->user);

        return $this->canSendSmsFlag;
    }

    public function format(): array
    {
        return array_map(static fn (SmsFromNumber $phone) => $phone->format(), $this->getList());
    }

    private function getSmsFromNumbers(): array
    {
        if ($this->fromNumbers !== null) {
            return $this->fromNumbers;
        }

        $this->fromNumbers = (new SmsFromNumberList($this->user->id, $this->lead->project_id, $this->lead->l_dep_id))->getList();

        return $this->fromNumbers;
    }
}
