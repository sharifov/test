<?php

namespace sales\services\caseSale;

/**
 * Class PnrPreparingService
 */
class PnrPreparingService
{
    private string $sourcePnr;
    private ?string $pnr = null;
    private string $separator = ',';
    private string $pattern = '/([A-Z\d]{6})/';

    public function __construct(string $sourcePnr)
    {
        $this->sourcePnr = $sourcePnr;
        $this->preparePnr();
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

    public function setSeparator(string $separator): PnrPreparingService
    {
        $this->separator = $separator;
        return $this;
    }

    public function setPattern(string $pattern): PnrPreparingService
    {
        $this->pattern = $pattern;
        return $this;
    }
}
