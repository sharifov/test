<?php

namespace src\model\sms\useCase\send\fromLead;

use common\models\Employee;
use common\models\Lead;
use modules\lead\src\abac\communicationBlock\LeadCommunicationBlockAbacObject;
use modules\lead\src\abac\communicationBlock\LeadCommunicationBlockAbacDto;
use src\model\phoneList\services\AvailablePhoneNumber;
use src\model\phoneList\services\AvailablePhoneNumberList;
use Yii;

/**
 * Class AbacSmsFromNumberList
 *
 * @property AvailablePhoneNumber[]|null $list
 * @property AvailablePhoneNumber[]|null $smsFromNumbers
 * @property Employee $user
 * @property Lead $lead
 * @property bool|null $canSendSmsFlag
 */
class AbacSmsFromNumberList
{
    private ?array $list = null;
    private ?array $smsFromNumbers = null;
    private Employee $user;
    private Lead $lead;
    private ?bool $canSendSmsFlag = null;

    public function __construct(Employee $user, Lead $lead)
    {
        $this->user = $user;
        $this->lead = $lead;
    }

    /**
     * @return AvailablePhoneNumber[]
     */
    public function getList(): array
    {
        if ($this->list !== null) {
            return $this->list;
        }

        $this->list = [];

        if ($this->canSendSms()) {
            foreach ($this->getSmsFromNumbers() as $number) {
                $tempAbacDto = new LeadCommunicationBlockAbacDto($this->lead, [], [$number], [], $this->user->id);
                if (Yii::$app->abac->can($tempAbacDto, LeadCommunicationBlockAbacObject::NS, LeadCommunicationBlockAbacObject::ACTION_SEND_SMS, $this->user)) {
                    $this->list[] = $number;
                }
                unset($tempAbacDto);
            }
        }

        return $this->list;
    }

    public function isExist(string $phoneNumber): bool
    {
        foreach ($this->getList() as $number) {
            if ($number->isEqual($phoneNumber)) {
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
        $list = [];
        foreach ($this->getList() as $number) {
            $list[$number->phone] = $number->format();
        }
        return $list;
    }

    public function first(): ?string
    {
        $list = $this->getList();
        if (!$list) {
            return null;
        }
        return $list[0]->phone;
    }

    private function getSmsFromNumbers(): array
    {
        if ($this->smsFromNumbers !== null) {
            return $this->smsFromNumbers;
        }

        $departmentParams = $this->lead->lDep->getParams();

        $numbers = new AvailablePhoneNumberList(
            $this->user->id,
            $this->lead->project_id,
            $this->lead->l_dep_id,
            $departmentParams ? $departmentParams->object->lead->callDefaultPhoneType : null
        );

        $this->smsFromNumbers = $numbers->getList();

        return $this->smsFromNumbers;
    }
}
