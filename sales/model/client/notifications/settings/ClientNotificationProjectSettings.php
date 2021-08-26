<?php

namespace sales\model\client\notifications\settings;

interface ClientNotificationProjectSettings
{
    public function isAnyTypeNotificationEnabled(int $projectId, string $type): bool;
    public function isSendPhoneNotificationEnabled(int $projectId, string $type): bool;
    public function isSendSmsNotificationEnabled(int $projectId, string $type): bool;
    public function isSendEmailNotificationEnabled(int $projectId, string $type): bool;
    public function getPhoneNotificationSettings(int $projectId, string $type): PhoneNotificationSettings;
}
