<?php

namespace src\services\caseSale;

/**
 * Class PnrPreparingService
 */
class PnrPreparingService
{
    private string $sourcePnr;
    private ?string $pnr = null;
    private string $separator;
    private string $pattern;

    public function __construct(string $sourcePnr, string $pattern = '/([A-Z\d]{6})/', string $separator = ',')
    {
        $this->sourcePnr = $sourcePnr;
        $this->setSeparator($separator)
            ->setPattern($pattern)
            ->preparePnr();
    }

    private function preparePnr(): void
    {
        $result = preg_match_all($this->pattern, $this->sourcePnr, $matches);

        if ($result) {
            $this->pnr = implode($this->separator, array_unique($matches[0]));
        }
    }

    public function getPnr(): ?string
    {
        return $this->pnr;
    }

    private function setSeparator(string $separator): PnrPreparingService
    {
        $this->separator = $separator;
        return $this;
    }

    private function setPattern(string $pattern): PnrPreparingService
    {
        $this->pattern = $pattern;
        return $this;
    }
}
