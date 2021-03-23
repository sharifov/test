<?php

namespace sales\services\quote\addQuote\guard;

use common\models\Employee;
use common\models\Lead;
use sales\helpers\setting\SettingHelper;

class FlightQuoteGuard
{
    public static ?bool $canAutoSelectQuotes = null;

    public static function canAutoSelectQuotes(Employee $employee, Lead $lead): bool
    {
        if (self::$canAutoSelectQuotes !== null) {
            return self::$canAutoSelectQuotes;
        }

        if (!(SettingHelper::getFlightQuoteAutoSelectCount() > 0)) {
            return false;
        }

        if ($employee->can('lead-view/flight/quote/auto-select') && $employee->can('lead/manage', ['lead' => $lead])) {
            return self::$canAutoSelectQuotes = true;
        }

        return self::$canAutoSelectQuotes = false;
    }
}
