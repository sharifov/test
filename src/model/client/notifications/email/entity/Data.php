<?php

namespace src\model\client\notifications\email\entity;

use yii\helpers\Json;

/**
 * Class Data
 *
 * @property int|null $clientId
 * @property int|null $caseId
 * @property int|null $projectId
 * @property string|null $projectKey
 * @property string|null $templateKey
 * @property int|null $productQuoteId
 */
class Data
{
    public ?int $clientId;
    public ?int $caseId;
    public ?int $projectId;
    public ?string $projectKey;
    public ?string $templateKey;
    public ?int $productQuoteId;

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
            ], 'ClientNotificationEmailListData');
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
        $data->projectKey = !empty($raw['projectKey']) ? (string)$raw['projectKey'] : null;
        $data->templateKey = !empty($raw['templateKey']) ? (string)$raw['templateKey'] : null;
        $data->productQuoteId = !empty($raw['productQuoteId']) ? (int)$raw['productQuoteId'] : null;
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
            'projectKey' => $this->projectKey,
            'templateKey' => $this->templateKey,
            'productQuoteId' => $this->productQuoteId,
        ];
    }

    private function loadDefaultValue(): void
    {
        $this->clientId = null;
        $this->caseId = null;
        $this->projectId = null;
        $this->projectKey = null;
        $this->templateKey = null;
        $this->productQuoteId = null;
    }
}
