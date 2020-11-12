<?php

namespace sales\model\call\entity\call\data;

use yii\helpers\Json;

/**
 * Class Data
 *
 * @property Repeat $repeat
 */
class Data
{
    public Repeat $repeat;

    public function __construct(?string $json)
    {
        if (!$json) {
            $this->repeat = new Repeat([]);
            return;
        }
        try {
            $data = Json::decode($json);
            $repeatData = empty($data['repeat']) ? [] : $data['repeat'];
            $this->repeat = new Repeat($repeatData);

        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'json' => $json,
            ], 'CallData');
        }
    }

    public function toJson(): string
    {
        return Json::encode([
            'repeat' => $this->repeat->toArray(),
        ]);
    }
}
