<?php

namespace sales\model\client\notifications\phone\entity;

use yii\helpers\Json;

/**
 * Class Data
 *
 * @property int|null $clientId
 * @property int|null $caseId
 * @property int|null $projectId
 * @property string|null $sayVoice
 * @property string|null $sayLanguage
 */
class Data
{
    public ?int $clientId;
    public ?int $caseId;
    public ?int $projectId;
    public ?string $sayVoice;
    public ?string $sayLanguage;

    private function __construct()
    {
    }

    public static function createFromJson(?string $json): self
    {
        if (!$json) {
            $data = new self();
            $data->loadDefaultValue();
            return $data;
        }
        try {
            $raw = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            return self::createFromArray($raw);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'json' => $json,
            ], 'ClientNotificationPhoneListData');
            $data = new self();
            $data->loadDefaultValue();
            return $data;
        }
    }

    public static function createFromArray(array $raw): self
    {
        $data = new self();
        $data->clientId = !empty($raw['clientId']) ? (int)$raw['clientId'] : null;
        $data->caseId = !empty($raw['caseId']) ? (int)$raw['caseId'] : null;
        $data->projectId = !empty($raw['projectId']) ? (int)$raw['projectId'] : null;
        $data->sayVoice = !empty($raw['sayVoice']) ? (string)$raw['sayVoice'] : null;
        $data->sayLanguage = !empty($raw['sayLanguage']) ? (string)$raw['sayLanguage'] : null;
        return $data;
    }

    public function toJson(): string
    {
        return Json::encode($this->toArray());
    }

    public function toArray(): array
    {
        return [
            'clientId' => $this->clientId,
            'caseId' => $this->caseId,
            'projectId' => $this->projectId,
            'sayVoice' => $this->sayVoice,
            'sayLanguage' => $this->sayLanguage,
        ];
    }

    private function loadDefaultValue(): void
    {
        $this->clientId = null;
        $this->caseId = null;
        $this->projectId = null;
        $this->sayVoice = null;
        $this->sayLanguage = null;
    }
}
