<?php

namespace src\model\clientChat\useCase\leadAutoTake;

use common\models\Lead;
use src\helpers\setting\SettingHelper;
use src\model\clientChatChannel\entity\ClientChatChannel;
use src\model\clientChatChannel\entity\ClientChatChannelDefaultSettings;

/**
 * Class ClientChatLeadAutoTakeSettings
 *
 * @property array $availableStatuses
 * @property bool|null $onAcceptChat
 */
class ClientChatLeadAutoTakeSettings
{
    private array $availableStatuses = [];
    private ?bool $onAcceptChat = null;

    public function __construct(ClientChatChannel $channel)
    {
        $channelSettings = $channel->ccc_settings ? json_decode($channel->ccc_settings, true) : [];
        $settings = array_merge(
            ClientChatChannelDefaultSettings::getAll(),
            $channelSettings
        );

        $statuses = (array)($settings['leadAutoTake']['availableStatuses'] ?? []);
        if ($statuses) {
            $this->availableStatuses = self::mapStatuses($statuses);
        }

        if (isset($settings['leadAutoTake']['onChatAccept'])) {
            $this->onAcceptChat = (bool)$settings['leadAutoTake']['onChatAccept'];
        }
    }

    public function isOnAcceptChat(): bool
    {
        return $this->onAcceptChat ?? SettingHelper::isClientChatLeadAutoTakeOnChatAccept();
    }

    public function getAvailableStatuses(): array
    {
        return $this->availableStatuses;
    }

    private static function mapStatuses(array $statuses): array
    {
        $leadStatuses = array_flip(Lead::STATUS_LIST);
        $mappedStatuses = array_map(static function ($status) use ($leadStatuses) {
            return $leadStatuses[$status] ?? null;
        }, $statuses);
        return array_filter($mappedStatuses, static fn ($value) => $value !== null);
    }
}
