<?php

namespace sales\repositories\clientChatChannel;

use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\repositories\NotFoundException;

class ClientChatChannelRepository
{
    /**
     * @param int|null $depId
     * @param int|null $projectId
     * @param int|null $priority
     * @return ClientChatChannel
     */
    public function findByClientChatData(?int $depId, ?int $projectId, ?int $priority): ClientChatChannel
    {
        $channel = ClientChatChannel::find();

        $channel->byDepartment($depId);
        $channel->byProject($projectId);

        if ($priority) {
            $channel->priority($priority);
        }

        $channel = $channel->orderBy(['ccc_priority' => SORT_ASC])->one();


        if ($channel) {
            return $channel;
        }

        throw new NotFoundException('Channel Not Found');
    }

    /**
     * @param int $userId
     * @param int|null $projectId
     * @param int|null $exceptDepartment
     * @param array|null $exceptChannels
     * @return ClientChatChannel[]
     */
    public function getByUserAndProject(int $userId, ?int $projectId, ?int $exceptDepartment = null, ?array $exceptChannels = null): array
    {
        $channelQuery = ClientChatChannel::find();
        $channelQuery->joinWithCcuc($userId);
        if ($projectId) {
            $channelQuery->byProject($projectId);
        }
        if ($exceptDepartment) {
            $channelQuery->exceptDepartment($exceptDepartment);
        }
        if ($exceptChannels) {
            $channelQuery->exceptChannels($exceptChannels);
        }
        $channels = $channelQuery->asArray()->all();
        if ($channels) {
            return $channels;
        }
        throw new NotFoundException('Channels not found');
    }

    public function findDefaultByProject(int $projectId): ClientChatChannel
    {
        if (!$clientChatChannel = ClientChatChannel::findOne(['ccc_default' => 1, 'ccc_project_id' => $projectId])) {
            throw new NotFoundException('Default Channel is not found by project(' . $projectId . ')');
        }
        return $clientChatChannel;
    }

    public function find(int $id): ClientChatChannel
    {
        if ($channel = ClientChatChannel::findOne(['ccc_id' => $id])) {
            return $channel;
        }
        throw new NotFoundException('Client Chat Channel not found');
    }
}
