<?php

namespace src\model\call\useCase\createCall\fromCase;

use common\models\Employee;
use modules\cases\src\abac\communicationBlock\CaseCommunicationBlockAbacObject;
use modules\cases\src\abac\communicationBlock\CaseCommunicationBlockAbacDto;
use src\entities\cases\Cases;
use src\model\phoneList\services\AvailablePhoneNumber;
use src\model\phoneList\services\AvailablePhoneNumberList;
use Yii;

/**
 * Class AbacCallFromNumberList
 *
 * @property AvailablePhoneNumber[]|null $list
 * @property AvailablePhoneNumber[]|null $callFromNumbers
 * @property Employee $user
 * @property Cases $case
 * @property bool|null $canCreateCall
 */
class AbacCallFromNumberList
{
    private ?array $list = null;
    private ?array $callFromNumbers = null;
    private Employee $user;
    private Cases $case;
    private ?bool $canCreateCall = null;

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

        if ($this->canMakeCall()) {
            foreach ($this->getCallFromNumbers() as $number) {
                $tempAbacDto = new CaseCommunicationBlockAbacDto($this->case, [$number], [], [], $this->user->id);
                /** @abac $tempAbacDto, CaseCommunicationBlockAbacObject::NS, CaseCommunicationBlockAbacObject::ACTION_MAKE_CALL, Validate Call From number for make call from Case View page */
                if (Yii::$app->abac->can($tempAbacDto, CaseCommunicationBlockAbacObject::NS, CaseCommunicationBlockAbacObject::ACTION_MAKE_CALL, $this->user)) {
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

    public function canMakeCall(): bool
    {
        if ($this->canCreateCall !== null) {
            return $this->canCreateCall;
        }

        $caseCommunicationBlockAbacDto = new CaseCommunicationBlockAbacDto($this->case, $this->getCallFromNumbers(), [], [], $this->user->id);
        /** @abac $caseCommunicationBlockAbacDto, CaseCommunicationBlockAbacObject::NS, CaseCommunicationBlockAbacObject::ACTION_MAKE_CALL, Validate Call From number list for make call from Case View page */
        $this->canCreateCall = (bool)Yii::$app->abac->can($caseCommunicationBlockAbacDto, CaseCommunicationBlockAbacObject::NS, CaseCommunicationBlockAbacObject::ACTION_MAKE_CALL, $this->user);

        return $this->canCreateCall;
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

    private function getCallFromNumbers(): array
    {
        if ($this->callFromNumbers !== null) {
            return $this->callFromNumbers;
        }

        $departmentParams = $this->case->cs_dep_id ? $this->case->department->getParams() : null;

        $numbers = new AvailablePhoneNumberList(
            $this->user->id,
            $this->case->cs_project_id,
            $this->case->cs_dep_id,
            $departmentParams ? $departmentParams->object->case->callDefaultPhoneType->isGeneral() : false
        );

        $this->callFromNumbers = $numbers->getList();

        return $this->callFromNumbers;
    }
}
