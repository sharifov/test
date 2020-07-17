<?php
namespace sales\model\clientChatNote;

use sales\auth\Auth;
use sales\model\clientChatNote\entity\ClientChatNote;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class ClientChatNoteRepository
 */
class ClientChatNoteRepository extends Repository
{
	public function save(ClientChatNote $clientChatNote): ClientChatNote
	{
		if (!$clientChatNote->save(false)) {
			throw new \RuntimeException('Client Chat Note saving failed');
		}
		return $clientChatNote;
	}

	public function checkForDelete(ClientChatNote $clientChatNote): bool
    {
        $user = Auth::user();
        return (($clientChatNote->ccn_user_id === $user->id) || ($user->isAdmin() || $user->isSuperAdmin()));
    }

	public function markDeleted(ClientChatNote $clientChatNote): ClientChatNote
	{
		if ($this->checkForDelete($clientChatNote)) {
            $clientChatNote->ccn_deleted = true;
            $this->save($clientChatNote);
		}
		return $clientChatNote;
	}

	public function toggleDeleted(ClientChatNote $clientChatNote): ClientChatNote
	{
		if ($this->checkForDelete($clientChatNote)) {
            $clientChatNote->ccn_deleted = $clientChatNote->ccn_deleted ? false : true ;
            $this->save($clientChatNote);
		}
		return $clientChatNote;
	}

	public function findById(int $id): ClientChatNote
	{
		if ($clientChat = ClientChatNote::findOne($id)) {
			return $clientChat;
		}
		throw new NotFoundException('Client chat note is not found');
	}
}