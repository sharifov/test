<?php

namespace sales\parcingDump\worldspanGds;

/**
 * Class Baggage
 */
class Baggage /* implements ParseDump*/
{
    /**
     * @param string $string
     * @return array
     */
    public function parseDump(string $string): array
    {
        $result['baggage'] = $this->parseBaggage($string);

        \yii\helpers\VarDumper::dump([$result, $string], 10, true); exit();
            /* FOR DEBUG:: must by remove */

        return $result;
    }

    /**
     * @param string $string
     * @return array|null
     */
    public function parseBaggage(string $string): ?array
    {
        $result = null;
        $baggagePattern = '/BAGGAGE ALLOWANCE(.*?)VIEWTRIP.TRAVELPORT.COM/s';
        preg_match($baggagePattern, $string, $baggageMatches);

        if (isset($baggageMatches[1])) {
            $bagPattern = '/BAG \d{1} - (.*?)CM/';
            preg_match_all($bagPattern, trim($baggageMatches[1]), $bagMatches);

            if (isset($bagMatches[1])) {
                foreach ($bagMatches[1] as $key => $value){
                    if ($bags = $this->prepareRow($value)) {
                        $result[$key]['price'] = $bags[0];
                        $result[$key]['currency'] = $bags[1];

                        $bagsInfo = array_slice($bags, 2);
                        $bagsInfo = implode(' ', $bagsInfo);
                        $result[$key]['info'] = $bagsInfo . 'CM';
                    }
                }
            }
        }
        return $result;
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
