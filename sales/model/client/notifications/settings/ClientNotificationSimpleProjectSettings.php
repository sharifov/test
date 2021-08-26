<?php

namespace sales\model\client\notifications\settings;

use common\models\Project;
use sales\model\project\entity\params\ClientNotificationObject;

/**
 * Class ClientNotificationSimpleProjectSettings
 *
 * @property array $settings
 */
class ClientNotificationSimpleProjectSettings implements ClientNotificationProjectSettings
{
    private array $settings = [];

    public function isAnyTypeNotificationEnabled(int $projectId, string $type): bool
    {
        return $this->isSendPhoneNotificationEnabled($projectId, $type);
    }

    public function isSendPhoneNotificationEnabled(int $projectId, string $type): bool
    {
        if (isset($this->settings['sendPhoneNotification'][$projectId][$type]['enabled'])) {
            return $this->settings['sendPhoneNotification'][$projectId][$type]['enabled'];
        }

        $project = Project::find()->byId($projectId)->one();

        if (!$project) {
            $this->settings['sendPhoneNotification'][$projectId][$type]['enabled'] = false;
            return $this->settings['sendPhoneNotification'][$projectId][$type]['enabled'];
        }

        if (!$project->getParams()->clientNotification->typeExist($type)) {
            $this->settings['sendPhoneNotification'][$projectId][$type]['enabled'] = false;
            return $this->settings['sendPhoneNotification'][$projectId][$type]['enabled'];
        }

        $typeNotification = $project->getParams()->clientNotification->$type;

        if (!$typeNotification instanceof ClientNotificationObject) {
            $this->settings['sendPhoneNotification'][$projectId][$type]['enabled'] = false;
            return $this->settings['sendPhoneNotification'][$projectId][$type]['enabled'];
        }

        if (!$typeNotification->sendPhoneNotification->enabled) {
            $this->settings['sendPhoneNotification'][$projectId][$type]['enabled'] = false;
            return $this->settings['sendPhoneNotification'][$projectId][$type]['enabled'];
        }

        $this->settings['sendPhoneNotification'][$projectId][$type] = [
            'enabled' => $typeNotification->sendPhoneNotification->enabled,
            'phoneFrom' => $typeNotification->sendPhoneNotification->phoneFrom,
            'messageSay' => $typeNotification->sendPhoneNotification->messageSay,
            'messageTemplateKey' => $typeNotification->sendPhoneNotification->messageTemplateKey,
            'messageSayVoice' => $typeNotification->sendPhoneNotification->messageSayVoice,
            'messageSayLanguage' => $typeNotification->sendPhoneNotification->messageSayLanguage,
            'fileUrl' => $typeNotification->sendPhoneNotification->fileUrl,
        ];

        return $this->settings['sendPhoneNotification'][$projectId][$type]['enabled'];
    }

    public function isSendSmsNotificationEnabled(int $projectId, string $type): bool
    {
        // TODO: Implement isSendSmsNotificationEnabled() method.
    }

    public function isSendEmailNotificationEnabled(int $projectId, string $type): bool
    {
        // TODO: Implement isSendEmailNotificationEnabled() method.
    }

    public function getPhoneNotificationSettings(int $projectId, string $type): PhoneNotificationSettings
    {
        if (!isset($this->settings['sendPhoneNotification'][$projectId][$type])) {
            throw new \DomainException('Not loaded sendPhoneNotification settings. ID: ' . $projectId . ' Type: ' . $type);
        }

        return new PhoneNotificationSettings(
            $this->settings['sendPhoneNotification'][$projectId][$type]['phoneFrom'],
            $this->settings['sendPhoneNotification'][$projectId][$type]['messageSay'],
            $this->settings['sendPhoneNotification'][$projectId][$type]['messageTemplateKey'],
            $this->settings['sendPhoneNotification'][$projectId][$type]['messageSayVoice'],
            $this->settings['sendPhoneNotification'][$projectId][$type]['messageSayLanguage'],
            $this->settings['sendPhoneNotification'][$projectId][$type]['fileUrl'],
        );
    }
}
