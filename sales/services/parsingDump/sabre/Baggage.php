<?php

namespace sales\services\parsingDump\Sabre;

use sales\helpers\app\AppHelper;
use sales\services\parsingDump\ParseDump;

/**
 * Class Baggage
 */
class Baggage implements ParseDump
{
    /**
     * @param string $string
     * @return array
     */
    public function parseDump(string $string): array
    {
        $result = [];
        try {
            if ($baggage = $this->parseBaggageAllowance($string)) {
                $result['baggage'] = $baggage;
            }
            if ($carryOnAllowance = $this->parseCarryOnAllowance($string)) {
                $result['carry_on_allowance'] = $carryOnAllowance;
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableFormatter($throwable), 'Sabre:Baggage:parseDump:Throwable');
        }
        return $result;
    }

    /**
     * @param string $string
     * @return array|null
     */
    public function parseCarryOnAllowance(string $string): ?array
    {
        $result = null;
        $carryPattern = '/CARRY ON ALLOWANCE(.*?)EMBARGO/s';
        preg_match($carryPattern, $string, $carryMatches);

        if (isset($carryMatches[1])) {
            $rowDelimPatten = '[A-Z]{2}\s[A-Z]{6}\s{1,2}\d{1}PC';
            $itemPattern = '/' . $rowDelimPatten . '(.*?)' . $rowDelimPatten . '/s';
            preg_match_all($itemPattern, $carryMatches[1], $itemMatches);

            preg_match_all('(' . $rowDelimPatten . ')', $carryMatches[1], $codeMatches);
            $items = preg_split('/' . $rowDelimPatten . '/', $carryMatches[1]);
            array_shift($items);

            if ($codeMatches[0]) {
                foreach ($codeMatches[0] as $key => $value) {

                    $info = $this->getBagInfo($value);
                    $result[$key]['iata'] = $info['iata'];
                    $result[$key]['code'] = $info['code'];
                    $result[$key]['allow_pieces'] = $info['allow_pieces'];

                    $itemRow = trim($items[$key]);
                    $itemRows = explode("\n", $itemRow);

                    foreach ($itemRows as $keyBag => $valueBag) {
                        preg_match("/BAG\s(\d{1})
                            \s-\s+(NO\sFEE)
                            \s+(.*?)$
                            /xs", $valueBag, $bagMatches);

                        if (isset($bagMatches[3])) {
                            $result[$key]['bag'][$keyBag]['price'] = isset($bagMatches[2]) ? trim($bagMatches[2]) : null;
                            $result[$key]['bag'][$keyBag]['info'] = isset($bagMatches[3]) ? trim($bagMatches[3]) : null;
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param string $string
     * @return array|null
     */
    public function parseBaggageAllowance(string $string): ?array
    {
        $result = null;
        $baggagePattern = '/BAGGAGE ALLOWANCE(.*?)CARRY ON ALLOWANCE/s';
        preg_match($baggagePattern, $string, $baggageMatches);

        if (isset($baggageMatches[1])) {
            // AA SLCCDG  0PC
            $rowDelimPatten = '[A-Z]{2}\s[A-Z]{6}\s{1,2}\d{1}PC';
            preg_match_all('(' . $rowDelimPatten . ')', $baggageMatches[1], $codeMatches);

            $items = preg_split('/' . $rowDelimPatten . '/', $baggageMatches[1]);
            array_shift($items);

            if ($codeMatches[0]) {
                foreach ($codeMatches[0] as $key => $value) {

                    $info = $this->getBagInfo($value);
                    $result[$key]['iata'] = $info['iata'];
                    $result[$key]['code'] = $info['code'];
                    $result[$key]['allow_pieces'] = $info['allow_pieces'];

                    $itemRow = trim($items[$key]);
                    $itemRows = explode("\n", $itemRow);

                    foreach ($itemRows as $keyBag => $valueBag) {
                        if (strlen($valueBag) < 10) {
                            continue;
                        }
                        // BAG 1 -  75.00 USD    UPTO50LB/23KG AND UPTO62LI/158LCM
                        // BAG 2 - NO FEE UPTO50LB/23KG AND UPTO81LI/208LCM
                        preg_match("/BAG\s(\d{1})
                                \s-\s+
                                ((NO\sFEE)|(\d*\.\d*)\s*([A-Z]{2,3}))                                
                                \s+(.*?)
                                \sAND\s(.*?)$
                                /xs", $valueBag, $bagMatches);

                        if (!empty($bagMatches)) {
                            $price = !empty($bagMatches[4]) ? $bagMatches[4] : $bagMatches[3];
                            $result[$key]['bag'][$keyBag]['price'] = trim($price);
                            $result[$key]['bag'][$keyBag]['currency'] = isset($bagMatches[5]) ? trim($bagMatches[5]) : null;
                            $result[$key]['bag'][$keyBag]['allow_max_weight'] = isset($bagMatches[6]) ? trim($bagMatches[6]) : null;
                            $result[$key]['bag'][$keyBag]['allow_max_size'] = isset($bagMatches[7]) ? trim($bagMatches[7]) : null;
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param string $text
     * @return array
     */
    private function getBagInfo(string $text): array
    {
        preg_match("/([A-Z]{2})\s([A-Z]{6})\s{1,2}(\d{1})PC/", $text, $rowInfoMatches);

        $result['iata'] = $rowInfoMatches[1] ?? null;
        $result['code'] = $rowInfoMatches[2] ?? null;
        $result['allow_pieces'] = $rowInfoMatches[3] ?? null;

        return $result;
    }
}
