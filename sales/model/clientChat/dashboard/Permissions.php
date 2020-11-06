<?php

namespace sales\model\clientChat\dashboard;

use sales\auth\Auth;

class Permissions
{
    private $channel;
    private $status;
    private $user;
    private $created_date;
    private $department;
    private $project;
    private $read_unread;
    private $group_my_chats;
    private $group_other_chats;
    private $group_free_to_take;
    private $client_name;

    public function canChannel(): bool
    {
        if ($this->channel !== null) {
            return $this->channel;
        }
        $this->channel = Auth::can('client-chat/dashboard/filter/channel');
        return $this->channel;
    }

    public function canStatus(): bool
    {
        if ($this->status !== null) {
            return $this->status;
        }
        $this->status = Auth::can('client-chat/dashboard/filter/status');
        return $this->status;
    }

    public function canUser(): bool
    {
        if ($this->user !== null) {
            return $this->user;
        }
        $this->user = Auth::can('client-chat/dashboard/filter/user');
        return $this->user;
    }

    public function canCreatedDate(): bool
    {
        if ($this->created_date !== null) {
            return $this->created_date;
        }
        $this->created_date = Auth::can('client-chat/dashboard/filter/created_date');
        return $this->created_date;
    }

    public function canDepartment(): bool
    {
        if ($this->department !== null) {
            return $this->department;
        }
        $this->department = Auth::can('client-chat/dashboard/filter/department');
        return $this->department;
    }

    public function canProject(): bool
    {
        if ($this->project !== null) {
            return $this->project;
        }
        $this->project = Auth::can('client-chat/dashboard/filter/project');
        return $this->project;
    }

    public function canReadUnread(): bool
    {
        if ($this->read_unread !== null) {
            return $this->read_unread;
        }
        $this->read_unread = Auth::can('client-chat/dashboard/filter/read_unread');
        return $this->read_unread;
    }

    public function canGroupMyChats(): bool
    {
        if ($this->group_my_chats !== null) {
            return $this->group_my_chats;
        }
        $this->group_my_chats = Auth::can('client-chat/dashboard/filter/group/my_chats');
        return $this->group_my_chats;
    }

    public function canGroupOtherChats(): bool
    {
        if ($this->group_other_chats !== null) {
            return $this->group_other_chats;
        }
        $this->group_other_chats = Auth::can('client-chat/dashboard/filter/group/other_chats');
        return $this->group_other_chats;
    }

    public function canGroupFreeToTake(): bool
    {
        if ($this->group_free_to_take !== null) {
            return $this->group_free_to_take;
        }
        $this->group_free_to_take = Auth::can('client-chat/dashboard/filter/group/free_to_take_chats');
        return $this->group_free_to_take;
    }

    public function canOneOfGroup(): bool
    {
        return $this->canGroupMyChats() || $this->canGroupOtherChats() || $this->canGroupFreeToTake();
    }

    public function canAllOfGroup(): bool
    {
        return $this->canGroupMyChats() && $this->canGroupOtherChats() && $this->canGroupFreeToTake();
    }

    public function canClientName(): bool
    {
        if ($this->client_name !== null) {
            return $this->client_name;
        }
        $this->client_name = Auth::can('client-chat/dashboard/filter/client_name');
        return $this->client_name;
    }

    public function canAdditionalFilter(): bool
    {
        return $this->canProject() || $this->canUser() || $this->canCreatedDate() || $this->canStatus() || $this->canClientName();
    }

}
