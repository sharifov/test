<?php

namespace sales\model\client\notifications\phone\entity;

use yii\helpers\Json;

/**
 * Class Data
 *
 * @property int|null $caseId
 */
class Data
{
    public ?int $caseId;

    public function __construct(?string $json)
    {
        if (!$json) {
            $this->loadDefaultValue();
            return;
        }
        try {
            $data = Json::decode($json);
            $this->caseId = empty($data['caseId']) ? null : (int)$data['caseId'];
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'json' => $json,
            ], 'ClientNotificationPhoneListData');
            $this->loadDefaultValue();
        }
    }

    public function toJson(): string
    {
        return Json::encode([
            'caseId' => $this->caseId,
        ]);
    }

    private function loadDefaultValue(): void
    {
        $this->caseId = null;
    }
}
