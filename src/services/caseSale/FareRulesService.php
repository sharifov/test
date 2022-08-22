<?php

namespace src\services\caseSale;

/**
 * Class FareRulesService
 */
class FareRulesService
{
    public const RULE_ELIGIBILITY      = 1;
    public const RULE_DAY_TIME         = 2;
    public const RULE_SEASONS          = 3;
    public const RULE_FLIGHT_APPL      = 4;
    public const RULE_ADV_RES_TKTG     = 5;
    public const RULE_MIN_STAY         = 6;
    public const RULE_MAX_STAY         = 7;
    public const RULE_STOPOVERS        = 8;
    public const RULE_TRANSFERS        = 9;
    public const RULE_COMBINATIONS     = 10;
    public const RULE_BLACKOUTS        = 11;
    public const RULE_SURCHARGES       = 12;
    public const RULE_ACCOMP_TRAVEL    = 13;
    public const RULE_TRAVEL_RESTR     = 14;
    public const RULE_SALES_RESTR      = 15;
    public const RULE_PENALTIES        = 16;
    public const RULE_HIP_MILEAGE      = 17;
    public const RULE_TICKET_ENDO      = 18;
    public const RULE_CHILDREN_DISC    = 19;
    public const RULE_TOUR_COND_DISC   = 20;
    public const RULE_AGENT_DISC       = 21;
    public const RULE_ALL_OTHER_DISC   = 22;
    public const RULE_MISC_PROVISIONS  = 23;
    public const RULE_FARE_BY_RULE     = 25;
    public const RULE_GROUPS           = 26;
    public const RULE_TOURS            = 27;
    public const RULE_VISIT_A_COUNTRY  = 28;
    public const RULE_DEPOSITS         = 29;
    public const RULE_VOLUNTARY_CHGS   = 31;
    public const RULE_VOLUNTARY_RFNDS  = 33;
    public const RULE_NEGOTIATED_FARES = 35;

    public const FARE_RULE_CATEGORY_LIST = [
        self::RULE_ELIGIBILITY => self::RULE_ELIGIBILITY,
        self::RULE_DAY_TIME => self::RULE_DAY_TIME,
        self::RULE_SEASONS => self::RULE_SEASONS,
        self::RULE_FLIGHT_APPL => self::RULE_FLIGHT_APPL,
        self::RULE_ADV_RES_TKTG => self::RULE_ADV_RES_TKTG,
        self::RULE_MIN_STAY => self::RULE_MIN_STAY,
        self::RULE_MAX_STAY => self::RULE_MAX_STAY,
        self::RULE_STOPOVERS => self::RULE_STOPOVERS,
        self::RULE_TRANSFERS => self::RULE_TRANSFERS,
        self::RULE_COMBINATIONS => self::RULE_COMBINATIONS,
        self::RULE_BLACKOUTS => self::RULE_BLACKOUTS,
        self::RULE_SURCHARGES => self::RULE_SURCHARGES,
        self::RULE_ACCOMP_TRAVEL => self::RULE_ACCOMP_TRAVEL,
        self::RULE_TRAVEL_RESTR => self::RULE_TRAVEL_RESTR,
        self::RULE_SALES_RESTR => self::RULE_SALES_RESTR,
        self::RULE_PENALTIES => self::RULE_PENALTIES,
        self::RULE_HIP_MILEAGE => self::RULE_HIP_MILEAGE,
        self::RULE_TICKET_ENDO => self::RULE_TICKET_ENDO,
        self::RULE_CHILDREN_DISC => self::RULE_CHILDREN_DISC,
        self::RULE_TOUR_COND_DISC => self::RULE_TOUR_COND_DISC,
        self::RULE_AGENT_DISC => self::RULE_AGENT_DISC,
        self::RULE_ALL_OTHER_DISC => self::RULE_ALL_OTHER_DISC,
        self::RULE_MISC_PROVISIONS => self::RULE_MISC_PROVISIONS,
        self::RULE_FARE_BY_RULE => self::RULE_FARE_BY_RULE,
        self::RULE_GROUPS => self::RULE_GROUPS,
        self::RULE_TOURS => self::RULE_TOURS,
        self::RULE_VISIT_A_COUNTRY => self::RULE_VISIT_A_COUNTRY,
        self::RULE_DEPOSITS => self::RULE_DEPOSITS,
        self::RULE_VOLUNTARY_CHGS => self::RULE_VOLUNTARY_CHGS,
        self::RULE_VOLUNTARY_RFNDS => self::RULE_VOLUNTARY_RFNDS,
        self::RULE_NEGOTIATED_FARES => self::RULE_NEGOTIATED_FARES,
    ];

    /**
     * @param array $saleWithFareRules
     * @param array $searchCategories
     * @return array
     */
    public function parseResponse(array $saleWithFareRules, array $searchCategories = [self::RULE_PENALTIES]): array
    {
        $fareRulesData = [];
        if (isset($saleWithFareRules['fareRules']) && count($saleWithFareRules['fareRules'])) {
            foreach ($saleWithFareRules['fareRules'] as $fareRules) {
                if (is_array($fareRules) && count($fareRules)) {
                    foreach ($fareRules['rules'] as $rule) {
                        if (is_array($rule) && in_array($rule['category'], $searchCategories)) {
                            $rule['fareBasisCode'] = $fareRules['fareBasisCode'] ?? '';
                            $fareRulesData[] = $rule;
                        }
                    }
                }
            }
        }

        return $fareRulesData;
    }
}
