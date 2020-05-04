<?php

namespace sales\services\client;

use common\models\DepartmentEmailProject;
use common\models\UserProjectParams;

/**
 * Class InternalEmailGuard
 *
 * @property array $internalEmails
 */
class InternalEmailGuard
{
    public $internalEamils;

    /**
     * @param string $email
     */
    public function guard(string $email): void
    {
        if (in_array($email, $this->getInternalEmails(), true)) {
            throw new InternalEmailException();
        }
    }

    private function getInternalEmails(): array
    {
        if ($this->internalEamils !== null) {
            return $this->internalEmails;
        }
        $this->internalEmails = array_merge([], $this->getDepartmentEmails(), $this->getUserProjectParams());
        return $this->internalEmails;
    }

    /**
     * @return array
     */
    private function getDepartmentEmails(): array
    {
        return DepartmentEmailProject::find()->select(['el_email'])->innerJoinWith('emailList', false)->column();
    }

    /**
     * @return array
     */
    private function getUserProjectParams(): array
    {
        return UserProjectParams::find()->select(['el_email'])->innerJoinWith('emailList', false)->column();
    }
}
