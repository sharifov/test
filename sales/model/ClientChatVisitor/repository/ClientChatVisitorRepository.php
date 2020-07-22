<?php


namespace sales\model\ClientChatVisitor\repository;


use sales\model\ClientChatVisitor\entity\ClientChatVisitor;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

class ClientChatVisitorRepository extends Repository
{
	public function create(int $clientId, string $visitorId): ClientChatVisitor
	{
		$clientChatVisitor = ClientChatVisitor::create($clientId, $visitorId);
		$this->save($clientChatVisitor);
		return $clientChatVisitor;
	}

	public function save(ClientChatVisitor $clientChatVisitor): int
	{
		if (!$clientChatVisitor->save()) {
			throw new \RuntimeException($clientChatVisitor->getErrorSummary(false)[0]);
		}
		return $clientChatVisitor->ccv_id;
	}

	public function existByClientRcId(string $rcId): bool
	{
		return ClientChatVisitor::find()->byVisitorRcId($rcId)->exists();
	}

	public function findByVisitorId(string $id): ClientChatVisitor
	{
		if ($visitor = ClientChatVisitor::findOne(['ccv_visitor_rc_id' => $id])) {
			return $visitor;
		}
		throw new NotFoundException('Client Chat Visitor is not found by visitor id: ' . $id);
	}
}