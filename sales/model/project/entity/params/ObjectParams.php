<?php

namespace sales\model\project\entity\params;

/**
 * Class ObjectParams
 *
 * @property CaseParams $case
 * @property QuoteParams $quote
 * @property LeadParams $lead
 */
class ObjectParams
{
    public CaseParams $case;
    public QuoteParams $quote;
    public LeadParams $lead;

    public function __construct(array $params)
    {
        $this->case = new CaseParams($params['case'] ?? self::default()['case']);
        $this->quote = new QuoteParams($params['quote'] ?? self::default()['quote']);
        $this->lead = new LeadParams($params['lead'] ?? self::default()['lead']);
    }

    public static function default(): array
    {
        return [
            'case' => CaseParams::default(),
            'quote' => QuoteParams::default(),
            'lead' => LeadParams::default(),
        ];
    }
}
