<?php

namespace sales\model\clientChat\permissions;

use sales\auth\Auth;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\entity\ClientChatQuery;

/**
 * Class ClientChatActionPermission
 *
 * @property bool|null $canClose
 * @property bool|null $canTransfer
 * @property bool|null $canCancelTransfer
 * @property bool|null $canNoteView
 * @property bool|null $canNoteAdd
 * @property bool|null $canNoteDelete
 * @property bool|null $canReopenChat
 * @property bool|null $canHold
 * @property bool|null $canUnHold
 * @property bool|null $canReturn
 * @property bool|null $canTake
 * @property bool|null $canSendCannedResponse
 */
class ClientChatActionPermission
{
    private ?bool $canClose = null;

    private ?bool $canTransfer = null;
    private ?bool $canCancelTransfer = null;

    private ?bool $canNoteView = null;
    private ?bool $canNoteAdd = null;
    private ?bool $canNoteDelete = null;

    private ?bool $canReopenChat = null;

    private ?bool $canHold = null;
    private ?bool $canUnHold = null;

    private ?bool $canReturn = null;

    private ?bool $canTake = null;
    private ?bool $canCouchNoteChecked = null;
    private ?bool $canSendCannedResponse = null;

    public function canClose(ClientChat $chat): bool
    {
        if ($this->canClose !== null) {
            return $this->canClose;
        }

        $systemRuleValid = !$chat->isClosed() && !$chat->isArchive();
        if (!$systemRuleValid) {
            $this->canClose = false;
            return $this->canClose;
        }

        $permissionsAccess = Auth::can('client-chat/manage', ['chat' => $chat]) && Auth::can('client-chat/close', ['chat' => $chat]);
        $this->canClose = $permissionsAccess;
        return $this->canClose;
    }

    public function canTransfer(ClientChat $chat): bool
    {
        if ($this->canTransfer !== null) {
            return $this->canTransfer;
        }

        $systemRuleValid = !$chat->isClosed() && !$chat->isArchive() && !$chat->isTransfer();
        if (!$systemRuleValid) {
            $this->canTransfer = false;
            return $this->canTransfer;
        }

        $this->canTransfer = Auth::can('client-chat/manage', ['chat' => $chat]) && Auth::can('client-chat/transfer', ['chat' => $chat]);
        return $this->canTransfer;
    }

    public function canCancelTransfer(ClientChat $chat): bool
    {
        if ($this->canCancelTransfer !== null) {
            return $this->canCancelTransfer;
        }

        $systemRuleValid = $chat->isTransfer();
        if (!$systemRuleValid) {
            $this->canCancelTransfer = false;
            return $this->canCancelTransfer;
        }

        $this->canCancelTransfer = Auth::can('client-chat/manage', ['chat' => $chat]) && Auth::can('client-chat/transfer_cancel', ['chat' => $chat]);
        return $this->canCancelTransfer;
    }

    public function canReopenChat(ClientChat $chat): bool
    {
        if ($this->canReopenChat !== null) {
            return $this->canReopenChat;
        }
        if (!$chat->isClosed()) {
            $this->canReopenChat = false;
            return $this->canReopenChat;
        }

        if (ClientChatQuery::isChildExistByChatId($chat->cch_id)) {
            $this->canReopenChat = false;
            return $this->canReopenChat;
        }

        $this->canReopenChat = Auth::can('client-chat/manage', ['chat' => $chat]) && Auth::can('client-chat/reopen', ['chat' => $chat]);
        return $this->canReopenChat;
    }

    public function canNoteView(ClientChat $chat): bool
    {
        if ($this->canNoteView !== null) {
            return $this->canNoteView;
        }
        $this->canNoteView = Auth::can('client-chat/view', ['chat' => $chat]) && Auth::can('client-chat/notes/view');
        return $this->canNoteView;
    }

    public function canNoteAdd(ClientChat $chat): bool
    {
        if ($this->canNoteAdd !== null) {
            return $this->canNoteAdd;
        }
        $this->canNoteAdd = Auth::can('client-chat/manage', ['chat' => $chat]) && Auth::can('client-chat/notes/add');
        return $this->canNoteAdd;
    }

    public function canNoteDelete(ClientChat $chat): bool
    {
        if ($this->canNoteDelete !== null) {
            return $this->canNoteDelete;
        }
        $this->canNoteDelete = Auth::can('client-chat/manage', ['chat' => $chat]) && Auth::can('client-chat/notes/delete');
        return $this->canNoteDelete;
    }

    public function canNoteShow(ClientChat $chat): bool
    {
        return ($this->canNoteView($chat) || $this->canNoteAdd($chat) || $this->canNoteDelete($chat));
    }

    public function canHold(ClientChat $chat): bool
    {
        if ($this->canHold !== null) {
            return $this->canHold;
        }

        $systemRuleValid = $chat->isInProgress();
        if (!$systemRuleValid) {
            $this->canHold = false;
            return $this->canHold;
        }

        $this->canHold = Auth::can('client-chat/manage', ['chat' => $chat]) && Auth::can('client-chat/hold', ['chat' => $chat]);
        return $this->canHold;
    }

    public function canUnHold(ClientChat $chat): bool
    {
        if ($this->canUnHold !== null) {
            return $this->canUnHold;
        }

        $systemRuleValid = $chat->isHold();
        if (!$systemRuleValid) {
            $this->canUnHold = false;
            return $this->canUnHold;
        }

        $this->canUnHold = Auth::can('client-chat/manage', ['chat' => $chat]) && Auth::can('client-chat/un_hold', ['chat' => $chat]);
        return $this->canUnHold;
    }

    public function canReturn(ClientChat $chat): bool
    {
        if ($this->canReturn !== null) {
            return $this->canReturn;
        }

        $systemRuleValid = $chat->isIdle() && $chat->isOwner(Auth::id());
        if (!$systemRuleValid) {
            $this->canReturn = false;
            return $this->canReturn;
        }

        $this->canReturn = Auth::can('client-chat/manage', ['chat' => $chat]) && Auth::can('client-chat/return', ['chat' => $chat]);
        return $this->canReturn;
    }

    public function canTake(ClientChat $chat): bool
    {
        if ($this->canTake !== null) {
            return $this->canTake;
        }

        $systemRuleValid = !$chat->isClosed() && !$chat->isArchive() && !$chat->isOwner(Auth::id());
        if (!$systemRuleValid) {
            $this->canTake = false;
            return $this->canTake;
        }

        $this->canTake = Auth::can('client-chat/view', ['chat' => $chat]) && Auth::can('client-chat/take', ['chat' => $chat]);
        return $this->canTake;
    }

    public function canSendCannedResponse(): ?bool
    {
        if ($this->canSendCannedResponse !== null) {
            return $this->canSendCannedResponse;
        }

        $this->canSendCannedResponse = Auth::can('client-chat/canned-response');
        return $this->canSendCannedResponse;
    }

    public function canCouchNote(?ClientChat $chat): bool
    {
        if ($this->canCouchNoteChecked !== null) {
            return $this->canCouchNoteChecked;
        }
        if (!$chat || $chat->isInClosedStatusGroup()) {
            return $this->canCouchNoteChecked = false;
        }

        $this->canCouchNoteChecked = Auth::can('client-chat/view', ['chat' => $chat]) &&
            Auth::can('client-chat/couch-note', ['chat' => $chat]);
        return $this->canCouchNoteChecked;
    }
}
