<?php

namespace sales\model\userModelSetting\service;

/**
 * Class UserModelSettingDictionary
 */
class UserModelSettingDictionary
{
    public const DT_TYPE_MY_CURRENT_SHIFT = 1;
    public const DT_TYPE_TODAY = 2;
    public const DT_TYPE_HOUR = 3;
    public const DT_TYPE_THREE_HOURS = 4;
    public const DT_TYPE_SIX_HOURS = 5;
    public const DT_TYPE_TWELVE_HOURS = 6;
    public const DT_TYPE_TWENTY_FOUR_HOURS = 7;

    public const DT_TYPE_LIST = [
        self::DT_TYPE_MY_CURRENT_SHIFT => 'My Current Shift',
        self::DT_TYPE_TODAY => 'Today',
        self::DT_TYPE_HOUR => 'Last Hour',
        self::DT_TYPE_THREE_HOURS => 'Last 3 Hours',
        self::DT_TYPE_SIX_HOURS => 'Last 6 Hours',
        self::DT_TYPE_TWELVE_HOURS => 'Last 12 Hours',
        self::DT_TYPE_TWENTY_FOUR_HOURS => 'Last 24 Hours',
    ];

    public const FIELD_NICKNAME = 'nickname';
    public const FIELD_LEAD_PROCESSING = 'lead_processing';
    public const FIELD_LEAD_TAKEN = 'lead_taken';
    public const FIELD_LEAD_SOLD = 'lead_sold';
    public const FIELD_LEAD_CREATED = 'lead_created';
    public const FIELD_LEAD_TRASHED = 'leads_trashed';
    public const FIELD_CLIENT_CHAT_ACTIVE = 'client_chat_active';
    public const FIELD_CLIENT_CHAT_IDLE = 'client_chat_idle';
    public const FIELD_CLIENT_CHAT_PROGRESS = 'client_chat_progress';
    public const FIELD_CLIENT_CHAT_CLOSED = 'client_chat_closed';
    public const FIELD_CLIENT_CHAT_TRANSFER = 'client_chat_transfer';

    public const FIELD_LIST = [
        self::FIELD_NICKNAME => 'Nickname',
        self::FIELD_LEAD_PROCESSING => 'Lead Processing',
        self::FIELD_LEAD_TAKEN => 'Leads Taken',
        self::FIELD_LEAD_SOLD => 'Leads Sold',
        self::FIELD_LEAD_CREATED => 'Leads Created',
        self::FIELD_LEAD_TRASHED => 'Leads Trashed',
        self::FIELD_CLIENT_CHAT_ACTIVE => 'Client Chat Active',
        self::FIELD_CLIENT_CHAT_IDLE => 'Client Chat Idle',
        self::FIELD_CLIENT_CHAT_PROGRESS => 'Client Chat in Progress',
        self::FIELD_CLIENT_CHAT_CLOSED => 'Client Chat Closed',
        self::FIELD_CLIENT_CHAT_TRANSFER => 'Client Chat Transfer',
    ];
}
