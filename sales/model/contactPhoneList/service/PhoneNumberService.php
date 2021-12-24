<?php

namespace sales\model\contactPhoneList\service;

use sales\services\phone\checkPhone\CheckPhoneService;

/**
 * Class PhoneNumberService
 */
class PhoneNumberService
{
    private string $phoneNumber;
    private string $uid;

    public function __construct(string $phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        $this->cleanNumber()->generateUid();
    }

    private function cleanNumber(): PhoneNumberService
    {
        $this->phoneNumber = CheckPhoneService::cleanPhone($this->phoneNumber);
        return $this;
    }

    private function generateUid(): void
    {
        $this->uid = CheckPhoneService::uidGenerator($this->phoneNumber);
    }

    public function getCleanedPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getUid(): string
    {
        return $this->uid;
    }
}
