<?php

namespace sales\parcingDump\worldspanGds;

use common\models\Airline;

/**
 * Class PricingPublished
 */
class PricingPublished /* implements ParseDump*/
{
    /**
     * @param string $string
     * @return array
     */
    public function parseDump(string $string): array
    {
        $result = [];
        $rowString = trim(preg_replace('!\s+!', ' ', $string));

        $result['iata'] = $this->parseIata($string);
        $result['airline'] = $this->getAirline($result['iata']);

        $ticketPricePattern = '/LAST DATE TO TICKET(.*?)\*LOWEST FARE/s';
        preg_match($ticketPricePattern, $string, $ticketPriceMatches);

        if (isset($ticketPriceMatches[1]) && $ticketPrice = trim($ticketPriceMatches[1])) {

            $ticketPrices = explode("\n", $ticketPrice);
            $totalRaw = end($ticketPrices);
            array_shift($ticketPrices);
            array_pop($ticketPrices);

            foreach ($ticketPrices as $key => $value) {
                if ($values = $this->preparePrice($value)) {
                    $price[$key]['name'] = $values[0] ?? null;
                    $price[$key]['base'] = $values[1] ?? null;
                    $price[$key]['fee'] = $values[2] ?? null;
                    $price[$key]['amount'] = $values[3] ?? null;
                }
            }
            if (isset($price)) {
                $result['price'] = $price;
            }
            if ($total = $this->preparePrice($totalRaw)) {
                $result['priceTotal']['base'] = $total[1] ?? null;
                $result['priceTotal']['fee'] = $total[2] ?? null;
                $result['priceTotal']['amount'] = $total[3] ?? null;
            }
        }

        \yii\helpers\VarDumper::dump([
                $ticketPriceMatches,
                $result,
                $string,
            ], 10, true); exit();
            /* FOR DEBUG:: must by remove */

        return $result;
    }

    public function parsePrice(string $string): ?string
    {
        $ticketPricePattern = '/LAST DATE TO TICKET(.*?)\*LOWEST FARE/s';
        preg_match($ticketPricePattern, $string, $ticketPriceMatches);

        if (isset($ticketPriceMatches[1]) && $ticketPrice = trim($ticketPriceMatches[1])) {

            $ticketPrices = explode("\n", $ticketPrice);
            $totalRaw = end($ticketPrices);
            array_shift($ticketPrices);
            array_pop($ticketPrices);

            foreach ($ticketPrices as $key => $value) {
                if ($values = $this->preparePrice($value)) {
                    $price[$key]['name'] = $values[0] ?? null;
                    $price[$key]['base'] = $values[1] ?? null;
                    $price[$key]['fee'] = $values[2] ?? null;
                    $price[$key]['amount'] = $values[3] ?? null;
                }
            }
            if (isset($price)) {
                $result['price'] = $price;
            }
            if ($total = $this->preparePrice($totalRaw)) {
                $result['priceTotal']['base'] = $total[1] ?? null;
                $result['priceTotal']['fee'] = $total[2] ?? null;
                $result['priceTotal']['amount'] = $total[3] ?? null;
            }
        }
    }

    /**
     * @param string $string
     * @return string|null
     */
    public function parseIata(string $string): ?string
    {
        $airlinePattern = '/CARRIER DEFAULT\s(.*?)\n\*{2}/';
        preg_match($airlinePattern, $string, $airlineMatches);
        return isset($airlineMatches[1]) ? trim($airlineMatches[1]) : null;
    }

    /**
     * @param string|null $iata
     * @param bool $asArray
     * @return array|Airline|null
     */
    public function getAirline(?string $iata, bool $asArray = true)
    {
        if ($iata) {
            if ($airline = Airline::findIdentity($iata)) {
                return $asArray ? $airline->toArray() : $airline;
            }
        }
        return null;
    }

    /**
     * @param string $row
     * @return false|string[]
     */
    private function preparePrice(string $row)
    {
        $value = trim($row);
        $value = preg_replace('|[\s]+|s', ' ', $value);
        return explode(' ', $value);
    }
}
