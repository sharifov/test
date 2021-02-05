<?php

namespace sales\model\call\entity\call\data;

use common\models\Call;
use yii\helpers\Json;

/**
 * Class Data
 *
 * @property Repeat $repeat
 * @property QueueLongTime $queueLongTime
 * @property CreatorType $creatorType
 * @property int $priority
 * @property array $createdParams
 * @property int|null $phoneListId
 */
class Data
{
    public Repeat $repeat;
    public QueueLongTime $queueLongTime;
    public CreatorType $creatorType;
    public int $priority;
    public array $createdParams;
    public ?int $phoneListId;

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
            $this->priority = empty($data['priority']) ? Call::DEFAULT_PRIORITY_VALUE : (int)$data['priority'];
            $this->createdParams = empty($data['createdParams']) ? [] : $data['createdParams'];
            $this->phoneListId = empty($data['phoneListId']) ? null : (int)$data['phoneListId'];
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
            'priority' => $this->priority,
            'createdParams' => $this->createdParams,
            'phoneListId' => $this->phoneListId,
        ]);
    }

    private function loadDefaultValue(): void
    {
        $this->repeat = new Repeat([]);
        $this->queueLongTime = new QueueLongTime([]);
        $this->creatorType = new CreatorType([]);
        $this->priority = Call::DEFAULT_PRIORITY_VALUE;
        $this->createdParams = [];
        $this->phoneListId = null;
    }
}
