<?php
namespace sales\repositories\clientChatChannel;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

class ClientChatChannelRepository extends Repository
{
	public function findByClientChatData(ClientChat $clientChat, ?int $priority): ClientChatChannel
	{
		$channel = ClientChatChannel::find();

		$channel->byDepartment($clientChat->cch_dep_id);
		$channel->byProject($clientChat->cch_project_id);

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