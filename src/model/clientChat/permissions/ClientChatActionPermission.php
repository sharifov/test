<?php

namespace src\model\clientChat\permissions;

use src\auth\Auth;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\entity\ClientChatQuery;

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
 * @property bool|null $canAcceptTransfer
 * @property bool|null $canSkipTransfer
 * @property bool|null $canAcceptPending
 * @property bool|null $canSkipPending
 * @property bool|null $canSendCannedResponse
 * @property bool|null $canCreateLead
 * @property bool|null $canCreateCase
 * @property bool|null $canLinkCase
 * @property bool|null $canLinkLead
 * @property bool|null $canUpdateChatStatus
 * @property bool|null $canViewChat
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
    private ?bool $canAcceptTransfer = null;
    private ?bool $canSkipTransfer = null;
    private ?bool $canAcceptPending = null;
    private ?bool $canSkipPending = null;
    private ?bool $canCouchNoteChecked = null;
    private ?bool $canSendCannedResponse = null;

    private ?bool $canCreateLead = null;
    private ?bool $canCreateCase = null;

    private ?bool $canLinkCase = null;
    private ?bool $canLinkLead = null;

    private ?bool $canUpdateChatStatus = null;

    private ?bool $canViewChat = null;

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

        $systemRuleValid = !$chat->isClosed() && !$chat->isArchive() && !$chat->isTransfer() && !$chat->isOwner(Auth::id());
        if (!$systemRuleValid) {
            $this->canTake = false;
            return $this->canTake;
        }

        $this->canTake = Auth::can('client-chat/view', ['chat' => $chat]) && Auth::can('client-chat/take', ['chat' => $chat]);
        return $this->canTake;
    }

    public function canAcceptTransfer(ClientChat $chat): bool
    {
        if ($this->canAcceptTransfer !== null) {
            return $this->canAcceptTransfer;
        }

        $systemRuleValid = $chat->isTransfer() && !$chat->isOwner(Auth::id());
        if (!$systemRuleValid) {
            $this->canAcceptTransfer = false;
            return $this->canAcceptTransfer;
        }

        $this->canAcceptTransfer = Auth::can('client-chat/view', ['chat' => $chat]) && Auth::can('client-chat/accept-transfer');
        return (bool)$this->canAcceptTransfer;
    }

    public function canSkipTransfer(ClientChat $chat): bool
    {
        if ($this->canSkipTransfer !== null) {
            return $this->canSkipTransfer;
        }

        $systemRuleValid = $chat->isTransfer();
        if (!$systemRuleValid) {
            $this->canSkipTransfer = false;
            return $this->canSkipTransfer;
        }

        $this->canSkipTransfer = Auth::can('client-chat/skip-transfer');
        return (bool)$this->canSkipTransfer;
    }

    public function canAcceptPending(ClientChat $chat): bool
    {
        if ($this->canAcceptPending !== null) {
            return $this->canAcceptPending;
        }

        $systemRuleValid = $chat->isPending();
        if (!$systemRuleValid) {
            $this->canAcceptPending = false;
            return $this->canAcceptPending;
        }

        $this->canAcceptPending = Auth::can('client-chat/accept-pending');
        return (bool)$this->canAcceptPending;
    }

    public function canSkipPending(ClientChat $chat): bool
    {
        if ($this->canSkipPending !== null) {
            return $this->canSkipPending;
        }

        $systemRuleValid = $chat->isPending();
        if (!$systemRuleValid) {
            $this->canSkipPending = false;
            return $this->canSkipPending;
        }

        $this->canSkipPending = Auth::can('client-chat/skip-pending');
        return (bool)$this->canSkipPending;
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

    public function canCreateLead(ClientChat $chat): bool
    {
        if ($this->canCreateLead !== null) {
            return $this->canCreateLead;
        }

        if ($chat->isClosed()) {
            $this->canCreateLead = false;
            return $this->canCreateLead;
        }

        $this->canCreateLead = Auth::can('/lead/create-by-chat') && Auth::can('client-chat/manage', ['chat' => $chat]);
        return $this->canCreateLead;
    }

    public function canCreateCase(ClientChat $chat): bool
    {
        if ($this->canCreateCase !== null) {
            return $this->canCreateCase;
        }

        if ($chat->isClosed()) {
            $this->canCreateCase = false;
            return $this->canCreateCase;
        }

        $this->canCreateCase = Auth::can('/cases/create-by-chat') && Auth::can('client-chat/manage', ['chat' => $chat]);
        return $this->canCreateCase;
    }

    public function canLinkCase(ClientChat $chat): bool
    {
        if ($this->canLinkCase !== null) {
            return $this->canLinkCase;
        }

        $this->canLinkCase = Auth::can('/cases/link-chat') && Auth::can('client-chat/manage', ['chat' => $chat]);
        return $this->canLinkCase;
    }

    public function canLinkLead(ClientChat $chat): bool
    {
        if ($this->canLinkLead !== null) {
            return $this->canLinkLead;
        }

        $this->canLinkLead = Auth::can('/lead/link-chat') && Auth::can('client-chat/manage', ['chat' => $chat]);
        return $this->canLinkLead;
    }

    public function canUpdateChatStatus(): bool
    {
        if ($this->canUpdateChatStatus !== null) {
            return $this->canUpdateChatStatus;
        }

        $this->canUpdateChatStatus = Auth::can('client-chat/accept-pending');
        return $this->canUpdateChatStatus;
    }

    public function canViewChat(): bool
    {
        if ($this->canViewChat !== null) {
            return $this->canViewChat;
        }

        $this->canViewChat = Auth::can('/client-chat/dashboard-v2');
        return $this->canViewChat;
    }
}
