<?php

namespace src\model\call\useCase\createCall\fromCase;

use common\models\Employee;
use modules\cases\src\abac\CasesAbacObject;
use modules\cases\src\abac\dto\CaseCommunicationBlockAbacDto;
use src\entities\cases\Cases;
use Yii;

/**
 * Class AbacPhoneList
 *
 * @property PhoneFrom[] $list
 * @property PhoneFrom[] $fromPhones
 * @property Employee $user
 * @property Cases $case
 * @property bool $canCreateCall
 */
class AbacPhoneList
{
    private ?array $list = null;
    private ?array $fromPhones = null;
    private Employee $user;
    private Cases $case;
    private ?bool $canCreateCall = null;

    public function __construct(Employee $user, Cases $case)
    {
        $this->user = $user;
        $this->case = $case;
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
                $tempAbacDto = new CaseCommunicationBlockAbacDto($this->case, [$fromPhone], $this->user->id);
                if (Yii::$app->abac->can($tempAbacDto, CasesAbacObject::OBJ_CASE_COMMUNICATION_BLOCK, CasesAbacObject::ACTION_MAKE_CALL, $this->user)) {
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

        $caseCommunicationBlockAbacDto = new CaseCommunicationBlockAbacDto($this->case, $this->getFromPhones(), $this->user->id);
        $this->canCreateCall = Yii::$app->abac->can($caseCommunicationBlockAbacDto, CasesAbacObject::OBJ_CASE_COMMUNICATION_BLOCK, CasesAbacObject::ACTION_MAKE_CALL, $this->user);

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

        $this->fromPhones = (new PhoneFromList($this->user->id, $this->case->cs_project_id, $this->case->cs_dep_id))->getList();

        return $this->fromPhones;
    }
}
