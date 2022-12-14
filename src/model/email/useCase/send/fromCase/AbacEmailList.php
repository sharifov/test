<?php

namespace src\model\email\useCase\send\fromCase;

use common\models\Employee;
use modules\cases\src\abac\communicationBlock\CaseCommunicationBlockAbacObject;
use modules\cases\src\abac\communicationBlock\CaseCommunicationBlockAbacDto;
use src\entities\cases\Cases;
use src\model\emailList\services\AvailableEmail;
use src\model\emailList\services\AvailableEmailList;
use Yii;

/**
 * Class AbacEmailList
 *
 * @property AvailableEmail[]|null $list
 * @property AvailableEmail[]|null $emailFromEmails
 * @property Employee $user
 * @property Cases $case
 */
class AbacEmailList
{
    private ?array $list = null;
    private ?array $emailFromEmails = null;
    private Employee $user;
    private Cases $case;

    public function __construct(Employee $user, Cases $case)
    {
        $this->user = $user;
        $this->case = $case;
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
            $tempAbacDto = new CaseCommunicationBlockAbacDto($this->case, [], [], [$email], $this->user->id);
            /** @abac $tempAbacDto, CaseCommunicationBlockAbacObject::NS, CaseCommunicationBlockAbacObject::ACTION_SEND_EMAIL, Validate Email From for send email from Case View page */
            if (Yii::$app->abac->can($tempAbacDto, CaseCommunicationBlockAbacObject::NS, CaseCommunicationBlockAbacObject::ACTION_SEND_EMAIL, $this->user)) {
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

        $departmentParams = $this->case->cs_dep_id ? $this->case->department->getParams() : null;

        $emails = new AvailableEmailList(
            $this->user->id,
            $this->case->cs_project_id,
            $this->case->cs_dep_id,
            $departmentParams ? $departmentParams->object->case->emailDefaultType->isGeneral() : false
        );

        $this->emailFromEmails = $emails->getList();

        return $this->emailFromEmails;
    }
}
