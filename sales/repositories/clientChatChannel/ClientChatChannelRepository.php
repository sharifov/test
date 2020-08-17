<?php
namespace sales\repositories\clientChatChannel;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

class ClientChatChannelRepository extends Repository
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
}