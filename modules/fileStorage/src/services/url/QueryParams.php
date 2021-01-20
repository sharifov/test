<?php

namespace modules\fileStorage\src\services\url;

/**
 * Class QueryParams
 *
 * @property array $params
 */
class QueryParams
{
    public const CONTEXT_LEAD = 'lead';
    public const CONTEXT_CASE = 'case';

    private array $params;

    private function __construct(array $params)
    {
        $this->params = $params;
    }

    public static function byEmpty(): self
    {
        return new static([]);
    }

    public static function byLead(): self
    {
        return new static(['context' => self::CONTEXT_LEAD]);
    }

    public static function byCase(): self
    {
        return new static(['context' => self::CONTEXT_CASE]);
    }

    public function build(): string
    {
        if (!$this->params) {
            return '';
        }
        return '&' . http_build_query($this->params);
    }
}
