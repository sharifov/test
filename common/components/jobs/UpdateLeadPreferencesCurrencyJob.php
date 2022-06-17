<?php

namespace common\components\jobs;

use common\models\Currency;
use common\models\LeadPreferences;
use common\models\Notifications;
use frontend\helpers\QuoteHelper;
use yii\queue\JobInterface;

class UpdateLeadPreferencesCurrencyJob extends BaseJob implements JobInterface
{
    private string $currencyCode;

    public function __construct(string $currencyCode, ?float $timeStart = null, $config = [])
    {
        $this->currencyCode = $currencyCode;

        parent::__construct($timeStart, $config);
    }

    public function execute($queue)
    {
        $this->waitingTimeRegister();

        $leadPreferences = LeadPreferences::getListByCurrencyCodeWhereLeadIsProcessed($this->currencyCode);

        if (count($leadPreferences) > 0) {
            $defaultCurrencyCode = Currency::getDefaultCurrency()->cur_code;
            $employees = [];

            foreach ($leadPreferences as $leadPreference) {
                $employeeID = $leadPreference->lead->employee_id;
                $leadPreference->pref_currency = $defaultCurrencyCode;
                $leadPreference->save(false);

                if ($leadPreference->save(false) === true) {
                    QuoteHelper::clearSearchCache($leadPreference->lead);

                    if (in_array($employeeID, $employees) === false) {
                        Notifications::createAndPublish(
                            $employeeID,
                            'Currency changed',
                            "The currency selected in Currency Preference has been disabled by the system administrator and can no longer be used for Price Quotes generation. All further Price Quotes will be generated in {$defaultCurrencyCode}.",
                            Notifications::TYPE_WARNING,
                            true
                        );

                        $employees[] = $employeeID;
                    }
                }
            }
        }
    }
}
