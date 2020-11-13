<?php
namespace sales\model\clientChat\useCase\cloneChat;

use sales\model\clientChat\entity\ClientChat;

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
 * @property int|null $isOnline
 * @property int|null $status
 * @property int|null $sourceTypeId
 * @property int|null $parentId
 * @property int|null $channelId
 * @property string|null $languageId
 */
class ClientChatCloneDto
{
	public $cchRid;
	public $cchCcrId;
	public $cchProjectId;
	public $cchDepId;
	public $cchClientId;
	public $ownerId;
	public $isOnline;
	public $status;
	public $sourceTypeId;
	public $parentId;
	public $channelId;
	public $languageId;

	public static function feelInOnCreateMessage(ClientChat $clientChat, int $clientChatRequestId): self
	{
		$_self = new self();
		$_self->cchRid = $clientChat->cch_rid;
		$_self->cchCcrId = $clientChatRequestId;
		$_self->cchProjectId = $clientChat->cch_project_id;
		$_self->cchClientId = $clientChat->cch_client_id;
		$_self->ownerId = null;
		$_self->isOnline = $clientChat->cch_client_online;
		return $_self;
	}

	public static function feelInOnTransfer(ClientChat $clientChat): self
	{
		$_self = new self();
		$_self->cchRid = $clientChat->cch_rid;
		$_self->cchCcrId = $clientChat->cch_ccr_id;
		$_self->cchProjectId = $clientChat->cch_project_id;
		$_self->cchClientId = $clientChat->cch_client_id;
		$_self->ownerId = null;
		$_self->isOnline = (int)$clientChat->cch_client_online;
		return $_self;
	}

    /**
     * @param ClientChat $clientChat
     * @param int $ownerId
     * @param int $parentId
     * @param int $sourceTypeId
     * @return static
     */
    public static function feelInOnTake(
        ClientChat $clientChat,
        int $ownerId,
        int $sourceTypeId = ClientChat::SOURCE_TYPE_TAKE
    ): self {
        $self = new self();
        $self->cchRid = $clientChat->cch_rid;
        $self->cchCcrId = $clientChat->cch_ccr_id;
        $self->cchProjectId = $clientChat->cch_project_id;
        $self->cchClientId = $clientChat->cch_client_id;
        $self->ownerId = $ownerId;
        $self->isOnline = (int)$clientChat->cch_client_online;
        $self->parentId = $clientChat->cch_id;
        $self->sourceTypeId = $sourceTypeId;
        $self->channelId = $clientChat->cch_channel_id;
        $self->languageId = $clientChat->cch_language_id;
        return $self;
    }
}