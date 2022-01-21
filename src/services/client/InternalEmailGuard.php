<?php

namespace src\services\client;

use src\model\emailList\entity\EmailList;

/**
 * Class InternalEmailGuard
 *
 * @property array $internalEmails
 */
class InternalEmailGuard
{
    private array $internalEmails = [];

    /**
     * @param string $email
     */
    public function guard(string $email): void
    {
        if (in_array($email, $this->getInternalEmails(), true)) {
            throw new InternalEmailException();
        }
    }

    public function getInternalEmails(): array
    {
        if (!empty($this->internalEmails)) {
            return $this->internalEmails;
        }
        $this->internalEmails = EmailList::find()->select(['el_email'])->column();
        return $this->internalEmails;
    }
}
