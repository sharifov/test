<?php

namespace src\model\sms\useCase\send\fromCase;

use common\models\Employee;
use modules\cases\src\abac\communicationBlock\CaseCommunicationBlockAbacObject;
use modules\cases\src\abac\communicationBlock\CaseCommunicationBlockAbacDto;
use src\entities\cases\Cases;
use src\model\phoneList\services\AvailablePhoneNumber;
use src\model\phoneList\services\AvailablePhoneNumberList;
use Yii;

/**
 * Class AbacSmsFromNumberList
 *
 * @property AvailablePhoneNumber[]|null $list
 * @property AvailablePhoneNumber[]|null $smsFromNumbers
 * @property Employee $user
 * @property Cases $case
 * @property bool|null $canSendSmsFlag
 */
class AbacSmsFromNumberList
{
    private ?array $list = null;
    private ?array $smsFromNumbers = null;
    private Employee $user;
    private Cases $case;
    private ?bool $canSendSmsFlag = null;

    public function __construct(Employee $user, Cases $case)
    {
        $this->user = $user;
        $this->case = $case;
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
                $tempAbacDto = new CaseCommunicationBlockAbacDto($this->case, [], [$number], [], $this->user->id);
                if (Yii::$app->abac->can($tempAbacDto, CaseCommunicationBlockAbacObject::NS, CaseCommunicationBlockAbacObject::ACTION_SEND_SMS, $this->user)) {
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

        $caseCommunicationBlockAbacDto = new CaseCommunicationBlockAbacDto($this->case, [], $this->getSmsFromNumbers(), [], $this->user->id);
        $this->canSendSmsFlag = Yii::$app->abac->can($caseCommunicationBlockAbacDto, CaseCommunicationBlockAbacObject::NS, CaseCommunicationBlockAbacObject::ACTION_SEND_SMS, $this->user);

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

        $departmentParams = $this->case->department->getParams();

        $numbers = new AvailablePhoneNumberList(
            $this->user->id,
            $this->case->cs_project_id,
            $this->case->cs_dep_id,
            $departmentParams ? $departmentParams->object->case->smsDefaultPhoneType->isGeneral() : false
        );

        $this->smsFromNumbers = $numbers->getList();

        return $this->smsFromNumbers;
    }
}
