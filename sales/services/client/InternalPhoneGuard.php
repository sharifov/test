<?php

namespace sales\services\client;

use sales\model\phoneList\entity\PhoneList;

/**
 * Class InternalPhoneGuard
 *
 * @property array $internalPhones
 */
class InternalPhoneGuard
{
    private array $internalPhones = [];

    /**
     * @param string $phone
     */
    public function guard(string $phone): void
    {
        if (in_array($phone, $this->getInternalPhones(), true)) {
            throw new InternalPhoneException();
        }
    }

    private function getInternalPhones(): array
    {
        if (!empty($this->internalPhones)) {
            return $this->internalPhones;
        }
        $this->internalPhones = PhoneList::find()->select(['pl_phone_number'])->column();
        return $this->internalPhones;
    }
}
