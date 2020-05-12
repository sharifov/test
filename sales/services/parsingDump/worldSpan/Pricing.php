<?php

namespace sales\services\parsingDump\worldSpan;

/**
 * Class Pricing
 */
class Pricing implements ParseDump
{
    /**
     * @param string $string
     * @return array
     */
    public function parseDump(string $string): array
    {
        $baggage = new Baggage();

        $result['iata'] = $this->parseIata($string);
        $result['price'] = $this->parsePrice($string);
        $result['baggage'] = $baggage->parseBaggageAllowance($string);
        $result['carry_on_allowance'] = $baggage->parseCarryOnAllowance($string);
        return $result;
    }

    /**
     * @param string $string
     * @return array|null
     */
    public function parsePrice(string $string): ?array
    {
        $result = null;
        $ticketPricePattern = '/LAST DATE TO TICKET(.*?)\*LOWEST FARE/s';
        preg_match($ticketPricePattern, $string, $ticketPriceMatches);

        if (isset($ticketPriceMatches[1]) && $ticketPrice = trim($ticketPriceMatches[1])) {
            $ticketPrices = explode("\n", $ticketPrice);
            $totalRaw = end($ticketPrices);
            array_shift($ticketPrices);
            array_pop($ticketPrices);

            foreach ($ticketPrices as $key => $value) {
                if ($values = $this->prepareRow($value)) {
                    $result['tickets'][$key]['name'] = $values[0] ?? null;
                    $result['tickets'][$key]['base'] = $values[1] ?? null;
                    $result['tickets'][$key]['fee'] = $values[2] ?? null;
                    $result['tickets'][$key]['amount'] = $values[3] ?? null;
                }
            }
            if ($total = $this->prepareRow($totalRaw)) {
                $result['priceTotal']['base'] = $total[1] ?? null;
                $result['priceTotal']['fee'] = $total[2] ?? null;
                $result['priceTotal']['amount'] = $total[3] ?? null;
            }
        }
        return $result;
    }

    /**
     * @param string $string
     * @return string|null
     */
    public function parseIata(string $string): ?string
    {
        $airlinePattern = '/CARRIER DEFAULT\s(.*?)\n\*/';
        preg_match($airlinePattern, $string, $airlineMatches);
        return isset($airlineMatches[1]) ? trim($airlineMatches[1]) : null;
    }

    /**
     * @param string $row
     * @return false|string[]
     */
    private function prepareRow(string $row)
    {
        $value = trim($row);
        $value = preg_replace('|[\s]+|s', ' ', $value);
        return explode(' ', $value);
    }
}
