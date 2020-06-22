<?php
namespace sales\repositories\clientChatChannel;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\repositories\Repository;

class ClientChatChannelRepository extends Repository
{
	public function findByClientChat(ClientChat $clientChat, int $priority): ClientChatChannel
	{
		$channel = ClientChatChannel::find();

		if ($clientChat->cch_dep_id) {
			$channel->byDepartment($clientChat->cch_dep_id);
		}

		if ($clientChat->cch_project_id) {
			$channel->byProject($clientChat->cch_project_id);
		}

		$channel = $channel->orderBy(['ccc_priority' => SORT_ASC])->priority($priority)->one();

		if ($channel) {
			return $channel;
		}

		throw new \RuntimeException('Channel Not Found');
	}
}