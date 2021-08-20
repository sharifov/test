<?php

namespace sales\model\project\entity\params;

/**
 * Class ObjectParams
 *
 * @property CaseParams $case
 * @property QuoteParams $quote
 */
class ObjectParams
{
    public CaseParams $case;
    public QuoteParams $quote;

    public function __construct(array $params)
    {
        $this->case = new CaseParams($params['case'] ?? self::default()['case']);
        $this->quote = new QuoteParams($params['quote'] ?? self::default()['quote']);
    }

    public static function default(): array
    {
        return [
            'case' => CaseParams::default(),
            'quote' => QuoteParams::default()
        ];
    }
}
