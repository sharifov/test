<?php

namespace modules\webEngage\settings;

use frontend\helpers\JsonHelper;
use sales\helpers\app\AppHelper;
use Yii;

/**
 * Class WebEngageSettings
 *
 * @property array $settings
 * @property bool $isEnabled
 * @property bool $isDebugEnable
 * @property bool $isTest
 * @property string $licenseCode
 * @property string $apiKey
 * @property string $trackingEventsHost
 * @property array $sourceCIds
 */
class WebEngageSettings
{
    private array $settings;
    private bool $isEnabled;
    private bool $isDebugEnable;
    private bool $isTest;
    private string $licenseCode;
    private string $apiKey;
    private string $trackingEventsHost;
    private array $sourceCIds;

    public function __construct()
    {
        if (empty(Yii::$app->params['settings']['web_engage']) ?? null) {
            throw new \RuntimeException('WebEngageSettings is empty');
        }
        try {
            $this->settings = (array) JsonHelper::decode(Yii::$app->params['settings']['web_engage']);
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'WebEngageSettings:Init:Throwable');
            throw new \RuntimeException('WebEngageSettings is corrupted');
        }
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled ?? (bool) ($this->settings['enable'] ?? false);
    }

    public function isDebugEnable(): bool
    {
        return $this->isDebugEnable ?? (bool) ($this->settings['debug_enable'] ?? false);
    }

    public function isTest(): bool
    {
        return $this->isTest ?? (bool) ($this->settings['is_test'] ?? false);
    }

    public function licenseCode(): string
    {
        if ($this->licenseCode ?? null) {
            return $this->licenseCode;
        }
        if ((!$licenseCode = $this->settings['license_code'] ?? null) || empty($licenseCode)) {
            throw new \RuntimeException('WebEngageSettings "license_code" is empty');
        }
        if (!is_string($licenseCode)) {
            throw new \RuntimeException('WebEngageSettings "license_code" is not string');
        }
        return $this->licenseCode = $licenseCode;
    }

    public function apiKey(): string
    {
        if ($this->apiKey ?? null) {
            return $this->apiKey;
        }
        if ((!$apiKey = $this->settings['api_key'] ?? null) || empty($apiKey)) {
            throw new \RuntimeException('WebEngageSettings "api_key" is empty');
        }
        if (!is_string($apiKey)) {
            throw new \RuntimeException('WebEngageSettings "api_key" is not string');
        }
        return $this->apiKey = $apiKey;
    }

    public function trackingEventsHost(): string
    {
        if ($this->trackingEventsHost ?? null) {
            return $this->trackingEventsHost;
        }
        if ((!$trackingEventsHost = $this->settings['tracking_events_host'] ?? null) || empty($trackingEventsHost)) {
            throw new \RuntimeException('WebEngageSettings "tracking_events_host" is empty');
        }
        if (!is_string($trackingEventsHost)) {
            throw new \RuntimeException('WebEngageSettings "tracking_events_host" is not string');
        }
        return $this->trackingEventsHost = $trackingEventsHost;
    }

    public function leadCreated(): array
    {
        return $this->getByEventName(WebEngageDictionary::EVENT_LEAD_CREATED);
    }

    public function leadCreatedApiUsernames(): ?string
    {
        $leadCreated = $this->leadCreated();
        return $leadCreated['apiUsernames'] ?? null;
    }

    public function getByEventName(string $eventName): array
    {
        if ((!$setting = $this->settings[$eventName] ?? null) || empty($setting)) {
            throw new \RuntimeException('WebEngageSettings "' . $eventName . '" is empty');
        }
        if (!is_array($setting)) {
            throw new \RuntimeException('WebEngageSettings "' . $eventName . '" is not array');
        }
        return $setting;
    }

    public function isSendUserCreateRequest(string $eventName): bool
    {
        $settings = $this->getByEventName($eventName);
        return (bool) ($settings['isSendUserCreateRequest'] ?? false);
    }

    public function sourceCIds(): array
    {
        if ($this->sourceCIds ?? null) {
            return $this->sourceCIds;
        }
        if (!array_key_exists('sourceCIds', $this->settings)) {
            throw new \RuntimeException('WebEngageSettings "sourceCIds" not found');
        }
        if (!is_array($this->settings['sourceCIds'])) {
            throw new \RuntimeException('WebEngageSettings "sourceCIds" is not array');
        }
        return $this->sourceCIds = $this->settings['sourceCIds'];
    }
}
