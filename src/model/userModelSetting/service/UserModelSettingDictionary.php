<?php

namespace src\model\userModelSetting\service;

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

    public const FIELD_AVATAR = 'avatar';
    public const FIELD_SHIFT_HOURS = 'shift_hours';
    public const FIELD_SHIFT_TIME = 'shift_time';
    public const FIELD_SALES_CONVERSION = 'sales_conversion';
    public const FIELD_SALES_CONVERSION_CALL_PRIORITY = 'sales_conversion_call_priority';
    public const FIELD_SUM_GROSS_PROFIT = 'sum_gross_profit';
    public const FIELD_GROSS_PROFIT_CALL_PRIORITY = 'gross_profit_call_priority';
    public const FIELD_SPLIT_SHARE = 'split_share';
    public const FIELD_CALL_PRIORITY_CURRENT = 'call_priority_current';
    public const FIELD_LEADS_QUALIFIED_COUNT = 'leads_qualified_count';
    public const FIELD_LEADS_QUALIFIED_TAKEN_COUNT = 'leads_qualified_taken_count';
    public const FIELD_LEADS_SOLD_COUNT = 'leads_sold_count';
    public const FIELD_CLIENT_PHONE = 'client_phone';
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
        self::FIELD_AVATAR => 'Avatar',
        self::FIELD_SHIFT_HOURS => 'Shift Hours',
        self::FIELD_SHIFT_TIME => 'Shift Time',
        self::FIELD_SALES_CONVERSION => 'Sales Conversion (Current Month)',
        self::FIELD_SUM_GROSS_PROFIT => 'Gross Profit',
        self::FIELD_GROSS_PROFIT_CALL_PRIORITY => 'Gross Profit (Call Priority)',
        self::FIELD_SALES_CONVERSION_CALL_PRIORITY => 'Sales Conversion (Call Priority)',
        self::FIELD_LEADS_SOLD_COUNT => 'Sold Leads (Current Month)',
        self::FIELD_LEADS_QUALIFIED_COUNT => 'Qualified Leads (Current Month)',
        self::FIELD_LEADS_QUALIFIED_TAKEN_COUNT => 'Qualified Leads Taken',
        self::FIELD_SPLIT_SHARE => 'Split Share (Current Month)',
        self::FIELD_CALL_PRIORITY_CURRENT => 'Call Priority (Current)',
        self::FIELD_CLIENT_PHONE => 'Phone Status',
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
