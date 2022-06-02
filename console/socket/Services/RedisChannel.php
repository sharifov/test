<?php

namespace console\socket\Services;

class RedisChannel
{
    public const SUBSCRIBE_CHANNEL = 'subscribe-channel';
    public const UNSUBSCRIBE_CHANNEL = 'unsubscribe-channel';

    private array $channels = [
        self::SUBSCRIBE_CHANNEL => [],
        self::UNSUBSCRIBE_CHANNEL => []
    ];

    public function add(string $channel, int $requestId): void
    {
        if (!isset($this->channels[$channel])) {
            \Yii::$app->redis->publish(self::SUBSCRIBE_CHANNEL, $channel);
        }

        $this->channels[$channel][$requestId] = $requestId;
    }

    public function remove(string $channel, ?int $requestId = null): void
    {
        if (isset($this->channels[$channel][$requestId])) {
            unset($this->channels[$channel][$requestId]);

            if (isset($this->channels[$channel]) && empty($this->channels[$channel])) {
                \Yii::$app->redis->publish(self::UNSUBSCRIBE_CHANNEL, $channel);
                unset($this->channels[$channel]);
            }
        }
    }

    public function getList(): array
    {
        return $this->channels;
    }

    public function getNameList(): array
    {
        return array_keys($this->channels);
    }
}
