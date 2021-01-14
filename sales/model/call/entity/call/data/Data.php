<?php

namespace sales\model\call\entity\call\data;

use yii\helpers\Json;

/**
 * Class Data
 *
 * @property Repeat $repeat
 * @property QueueLongTime $queueLongTime
 * @property CreatorType $creatorType
 */
class Data
{
    public Repeat $repeat;
    public QueueLongTime $queueLongTime;
    public CreatorType $creatorType;

    public function __construct(?string $json)
    {
        if (!$json) {
            $this->loadDefaultValue();
            return;
        }
        try {
            $data = Json::decode($json);
            $this->repeat = new Repeat(empty($data['repeat']) ? [] : $data['repeat']);
            $this->queueLongTime = new QueueLongTime(empty($data['queueLongTime']) ? [] : $data['queueLongTime']);
            $this->creatorType = new CreatorType(empty($data['creatorType']) ? [] : $data['creatorType']);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'json' => $json,
            ], 'CallData');
            $this->loadDefaultValue();
        }
    }

    public function toJson(): string
    {
        return Json::encode([
            'repeat' => $this->repeat->toArray(),
            'queueLongTime' => $this->queueLongTime->toArray(),
            'creatorType' => $this->creatorType->toArray(),
        ]);
    }

    private function loadDefaultValue(): void
    {
        $this->repeat = new Repeat([]);
        $this->queueLongTime = new QueueLongTime([]);
        $this->creatorType = new CreatorType([]);
    }
}
