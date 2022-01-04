<?php

namespace sales\model\user\reports\stats;

class Metrics
{
    public const SALES_CONVERSION = 1;
    public const SOLD_LEADS = 2;
    public const SPLIT_SHARE = 3;
    public const QUALIFIED_LEADS_TAKEN = 4;
    public const GROSS_PROFIT = 5;
    public const TIPS = 6;
    public const LEADS_CREATED = 7;
    public const LEADS_PROCESSED = 8;
    public const LEADS_TRASHED = 9;
    public const LEADS_CLONED = 10;
    public const LEADS_TO_FOLLOW_UP = 11;
    public const SALES_CONVERSION_CALL_PRIORITY = 12;
    public const GROSS_PROFIT_CALL_PRIORITY = 13;
    public const CALL_PRIORITY_CURRENT = 14;

    public const LIST = [
        self::SALES_CONVERSION => 'Sales Conversion',
        self::SALES_CONVERSION_CALL_PRIORITY => 'Sales Conversion (Call Priority)',
        self::SOLD_LEADS => 'Sold Leads',
        self::SPLIT_SHARE => 'Split Share',
        self::QUALIFIED_LEADS_TAKEN => 'Qualified leads taken',
        self::GROSS_PROFIT => 'Gross Profit',
        self::GROSS_PROFIT_CALL_PRIORITY => 'Gross Profit (Call Priority)',
        self::CALL_PRIORITY_CURRENT => 'Call Priority (Current)',
        self::TIPS => 'Tips',
        self::LEADS_CREATED => 'Leads Created',
        self::LEADS_PROCESSED => 'Leads Processed',
        self::LEADS_TRASHED => 'Leads Trashed',
        self::LEADS_CLONED => 'Leads Cloned',
        self::LEADS_TO_FOLLOW_UP => 'Leads to Follow Up',
    ];

    public static function isSalesConversion(array $metrics): bool
    {
        return in_array(self::SALES_CONVERSION, $metrics, false);
    }

    public static function isSalesConversionCallPriority(array $metrics): bool
    {
        return in_array(self::SALES_CONVERSION_CALL_PRIORITY, $metrics, false);
    }

    public static function isSoldLeads(array $metrics): bool
    {
        return in_array(self::SOLD_LEADS, $metrics, false);
    }

    public static function isSplitShare(array $metrics): bool
    {
        return in_array(self::SPLIT_SHARE, $metrics, false);
    }

    public static function isQualifiedLeadsTaken(array $metrics): bool
    {
        return in_array(self::QUALIFIED_LEADS_TAKEN, $metrics, false);
    }

    public static function isGrossProfit(array $metrics): bool
    {
        return in_array(self::GROSS_PROFIT, $metrics, false);
    }

    public static function isGrossProfitCallPriority(array $metrics): bool
    {
        return in_array(self::GROSS_PROFIT_CALL_PRIORITY, $metrics, false);
    }

    public static function isTips(array $metrics): bool
    {
        return in_array(self::TIPS, $metrics, false);
    }

    public static function isLeadsCreated(array $metrics): bool
    {
        return in_array(self::LEADS_CREATED, $metrics, false);
    }

    public static function isLeadsProcessed(array $metrics): bool
    {
        return in_array(self::LEADS_PROCESSED, $metrics, false);
    }

    public static function isLeadsTrashed(array $metrics): bool
    {
        return in_array(self::LEADS_TRASHED, $metrics, false);
    }

    public static function isLeadsCloned(array $metrics): bool
    {
        return in_array(self::LEADS_CLONED, $metrics, false);
    }

    public static function isLeadsToFollowUp(array $metrics): bool
    {
        return in_array(self::LEADS_TO_FOLLOW_UP, $metrics, false);
    }

    public static function isCallPriorityCurrent(array $metrics): bool
    {
        return in_array(self::CALL_PRIORITY_CURRENT, $metrics, false);
    }
}
