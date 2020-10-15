<?php

namespace sales\model\clientChat\permissions;

use sales\auth\Auth;
use sales\model\clientChat\entity\ClientChat;

/**
 * Class ClientChatActionPermission
 *
 * @property bool|null $canClose
 * @property bool|null $canTransfer
 * @property bool|null $canNoteView
 * @property bool|null $canNoteAdd
 * @property bool|null $canNoteDelete
 * @property bool|null $canReopenChat
 * @property bool|null $canHold
 * @property bool|null $canUnHold
 * @property bool|null $canReturn
 */
class ClientChatActionPermission
{
    private ?bool $canClose = null;

    private ?bool $canTransfer = null;

    private ?bool $canNoteView = null;
    private ?bool $canNoteAdd = null;
    private ?bool $canNoteDelete = null;

    private ?bool $canReopenChat = null;

    private ?bool $canHold = null;
    private ?bool $canUnHold = null;

    private ?bool $canReturn = null;

    private ?bool $canTake = null;

    public function canClose(ClientChat $chat): bool
    {
        if ($this->canClose !== null) {
            return $this->canClose;
        }
        $this->canClose = Auth::can('client-chat/manage', ['chat' => $chat]) && Auth::can('client-chat/close', ['chat' => $chat]);
        return $this->canClose;
    }

    public function canTransfer(ClientChat $chat): bool
    {
        if ($this->canTransfer !== null) {
            return $this->canTransfer;
        }
        $this->canTransfer = Auth::can('client-chat/manage', ['chat' => $chat]) && Auth::can('client-chat/transfer', ['chat' => $chat]);
        return $this->canTransfer;
    }

    public function canReopenChat(ClientChat $chat): bool
    {
        if ($this->canReopenChat !== null) {
            return $this->canReopenChat;
        }
        $this->canReopenChat = Auth::can('client-chat/manage', ['chat' => $chat]) && Auth::can('client-chat/close/reopen', ['chat' => $chat]);
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

    public function canHold(ClientChat $chat): bool
    {
        if ($this->canHold !== null) {
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
        $this->canUnHold = Auth::can('client-chat/manage', ['chat' => $chat]) && Auth::can('client-chat/un_hold', ['chat' => $chat]);
        return $this->canUnHold;
    }

    public function canReturn(ClientChat $chat): bool
    {
        if ($this->canReturn !== null) {
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
        $this->canTake = Auth::can('client-chat/view', ['chat' => $chat]) && Auth::can('client-chat/take', ['chat' => $chat]);
        return $this->canTake;
    }
}
