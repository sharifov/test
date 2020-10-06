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
 */
class ClientChatActionPermission
{
    private ?bool $canClose = null;

    private ?bool $canTransfer = null;

    private ?bool $canNoteView = null;
    private ?bool $canNoteAdd = null;
    private ?bool $canNoteDelete = null;

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
}
