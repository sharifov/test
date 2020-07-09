<?php
namespace sales\model\clientChat\useCase\cloneChat;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\transfer\ClientChatTransferForm;

/**
 * Class ClientChatCloneDto
 * @package sales\model\clientChat\useCase\cloneChat
 *
 * @property string $cchRid
 * @property int $cchCcrId
 * @property int|null $cchProjectId
 * @property int|null $cchDepId
 * @property int|null $cchClientId
 * @property int|null $ownerId
 */
class ClientChatCloneDto
{
	public $cchRid;
	public $cchCcrId;
	public $cchProjectId;
	public $cchDepId;
	public $cchClientId;
	public $ownerId;

	public static function feelInOnCreateMessage(ClientChat $clientChat, int $clientChatRequestId): self
	{
		$_self = new self();
		$_self->cchRid = $clientChat->cch_rid;
		$_self->cchCcrId = $clientChatRequestId;
		$_self->cchProjectId = $clientChat->cch_project_id;
		$_self->cchDepId = $clientChat->cch_dep_id;
		$_self->cchClientId = $clientChat->cch_client_id;
		$_self->ownerId = null;
		return $_self;
	}

	public static function feelInOnTransfer(ClientChat $clientChat, ClientChatTransferForm $form): self
	{
		$_self = new self();
		$_self->cchRid = $clientChat->cch_rid;
		$_self->cchCcrId = $clientChat->cch_ccr_id;
		$_self->cchProjectId = $clientChat->cch_project_id;
		$_self->cchDepId = $form->depId;
		$_self->cchClientId = $clientChat->cch_client_id;
		$_self->ownerId = null;
		return $_self;
	}
}