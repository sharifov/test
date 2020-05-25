<?php

namespace sales\services\parsingDump\lib\worldSpan;

use sales\helpers\app\AppHelper;
use sales\services\parsingDump\lib\ParseDumpInterface;

/**
 * Class Baggage
 */
class Baggage implements ParseDumpInterface
{
    public array $segments;

    private string $rowDelimPatten = '[A-Z]{2}\s[A-Z]{6}\s{1,2}\d{1}PC';

    /**
     * @param string $string
     * @return array
     */
    public function parseDump(string $string): array
    {
        $result = [];
        try {
            $this->setSegments($string);

            if ($baggage = $this->processingResult($string)) {
                $result['baggage'] = $baggage;
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableFormatter($throwable), 'WorldSpan:Baggage:parseDump:Throwable');
        }
        return $result;
    }

    /**
     * @param string $dump
     * @return array
     */
    private function processingResult(string $dump): array
    {
        $result = [];
        $paidBaggage = $this->prepareBaggageAllowance($dump);
        $freeBaggage = $this->prepareCarryOnAllowance($dump);

        $i = 0;
        foreach ($this->segments as $segment) {
            $result[$i]['segment'] = $segment;

            if (!empty($paidBaggage) && array_key_exists($segment, $paidBaggage)) {
                $result[$i]['paid_baggage'] = $paidBaggage[$segment];
            } else {
                $result[$i]['paid_baggage'] = [];
            }
            if (!empty($freeBaggage) && array_key_exists($segment, $freeBaggage)) {
                $result[$i]['free_baggage'] = $freeBaggage[$segment][0];
            } else {
                $result[$i]['free_baggage'] = [];
            }
            $i ++;
        }
        return $result;
    }

    /**
     * @param string $string
     * @return array|null
     */
    public function prepareCarryOnAllowance(string $string): ?array
    {
        $result = null;
        $carryPattern = '/CARRY ON ALLOWANCE(.*?)EMBARGO/s';
        preg_match($carryPattern, $string, $carryMatches);

        if (isset($carryMatches[1])) {

            $itemPattern = '/' . $this->rowDelimPatten . '(.*?)' . $this->rowDelimPatten . '/s';
            preg_match_all($itemPattern, $carryMatches[1], $itemMatches);

            preg_match_all('(' . $this->rowDelimPatten . ')', $carryMatches[1], $codeMatches);
            $items = preg_split('/' . $this->rowDelimPatten . '/', $carryMatches[1]);
            array_shift($items);

            if ($codeMatches[0]) {
                foreach ($codeMatches[0] as $key => $value) {
                    $info = $this->getBagInfo($value);
                    $segment = $info['code'];
                    $itemRow = trim($items[$key]);
                    $itemRows = explode("\n", $itemRow);

                    // BAG 1 - NO FEE UPTO50LB/23KG AND UPTO50LB/127LCM
                    // BAG 2 - NO FEE UPTO45LI/115LCM
                    // BAG 3 - NO FEE CARRYON HAND BAGGAGE ALLOWANCE
                    foreach ($itemRows as $keyBag => $valueBag) {
                        preg_match(
                            "/BAG\s(\d{1})
                            \s-\s+(NO\sFEE)
                            \s+((.*?)\sAND\s(.*?)$|(.*?)\/(.*?)$|(.*?)$)
                            /xs",
                            $valueBag,
                            $bagMatches
                        );

                        if (isset($bagMatches[3])) {
                            $weightAlt = $bagMatches[6] ?? '';
                            $heightAlt = $bagMatches[7] ?? '';

                            $result[$segment][$keyBag]['piece'] = (int) $info['allow_pieces'];
                            $result[$segment][$keyBag]['price'] = 'USD0';
                            $result[$segment][$keyBag]['weight'] = $bagMatches[4] . $weightAlt;
                            $result[$segment][$keyBag]['height'] = $bagMatches[5] . $heightAlt;
                            if (isset($bagMatches[8])) {
                                $result[$segment][$keyBag]['info'] = $bagMatches[8];
                            }
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
    public function prepareBaggageAllowance(string $string): ?array
    {
        $result = null;
        $baggagePattern = '/BAGGAGE ALLOWANCE(.*?)CARRY ON ALLOWANCE/s';
        preg_match($baggagePattern, $string, $baggageMatches);

        if (isset($baggageMatches[1])) {
            preg_match_all('(' . $this->rowDelimPatten . ')', $baggageMatches[1], $codeMatches);

            $items = preg_split('/' . $this->rowDelimPatten . '/', $baggageMatches[1]);
            array_shift($items);

            if ($codeMatches[0]) {
                foreach ($codeMatches[0] as $key => $value) {
                    $info = $this->getBagInfo($value);
                    $segment = $info['code'];
                    $itemRow = trim($items[$key]);
                    $itemRows = explode("\n", $itemRow);

                    foreach ($itemRows as $keyBag => $valueBag) {
                        if (strlen($valueBag) < 10) {
                            continue;
                        }
                        // BAG 1 -  75.00 USD    UPTO50LB/23KG AND UPTO62LI/158LCM
                        // BAG 2 - NO FEE UPTO50LB/23KG AND UPTO81LI/208LCM
                        preg_match(
                            "/BAG\s(\d{1})
                                \s-\s+
                                ((NO\sFEE)|(\d*\.\d*)\s*([A-Z]{2,3}))                                
                                \s+(.*?)
                                \sAND\s(.*?)$
                                /xs",
                            $valueBag,
                            $bagMatches
                        );
                        if (!empty($bagMatches)) {
                            $price = !empty($bagMatches[4]) ? $bagMatches[4] : '0';
                            $currency = !empty($bagMatches[5]) ? trim($bagMatches[5]) : 'USD';

                            $result[$segment][$keyBag]['piece'] = (int) $info['allow_pieces'];
                            $result[$segment][$keyBag]['price'] = $currency . trim($price);
                            $result[$segment][$keyBag]['weight'] = isset($bagMatches[6]) ? trim($bagMatches[6]) : null;
                            $result[$segment][$keyBag]['height'] = isset($bagMatches[7]) ? trim($bagMatches[7]) : null;
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

     /**
     * @param string $string
     * @return Baggage
     */
    private function setSegments(string $string): Baggage
    {
        preg_match_all('(' . $this->rowDelimPatten . ')', $string, $codeMatches);
        if ($codeMatches[0]) {
            foreach ($codeMatches[0] as $key => $value) {
                $info = $this->getBagInfo($value);
                $this->segments[$info['code']] = $info['code'];
            }
        }
        return $this;
    }

}
