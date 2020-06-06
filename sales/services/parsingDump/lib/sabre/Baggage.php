<?php

namespace sales\services\parsingDump\lib\sabre;

use sales\helpers\app\AppHelper;
use sales\services\parsingDump\lib\ParseDumpInterface;

/**
 * Class Baggage
 */
class Baggage implements ParseDumpInterface
{
    /**
     * @param string $string
     * @return array
     */
    public function parseDump(string $string): array
    {
        $result = [];
        try {
            if ($baggage = $this->getParseDump($string)) {
                $result['baggage'] = $baggage;
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableFormatter($throwable), 'Sabre:Baggage:parseDump:Throwable');
        }
        return $result;
    }

    public function getParseDump($priceDump)
    {
        $explodeDump = explode("\n", $priceDump);

        $bagRows = [];
        foreach ($explodeDump as $key => $row) {
            $row = trim($row);
            if (stripos($row, "«") !== false) {
                continue;
            }

            if (stripos($row, "BAG ALLOWANCE") !== false) {
                $bagRows[] = $this->getBagString($explodeDump, $key);
            }
        }

        return  $bagRows;
    }

    private function getBagString($array, $index)
    {
        $bags = [];
        foreach ($array as $key => $val) {
            $val = trim($val);
            if ($key < $index) {
                continue;
            }
            if (stripos($val, "BAG ALLOWANCE") !== false && $key > $index){
                break;
            }
            $bags[] = $val;
            if (stripos($val, "**") !== false) {
                if (!isset($array[($key + 1)]) || stripos($array[($key + 1)], "2NDCHECKED") === false) {
                    break;
                }
            }
        }

        $bagsString = explode('2NDCHECKED', trim(implode(' ', $bags)));
        $bags = [
            'segment' => '',
            'free_baggage' => [],
            'paid_baggage' => []
        ];

        foreach ($bagsString as $key => $val) {
            $val = str_replace('*', '', $val);
            $detail = explode('-', $val);

            if (stripos($val, "BAG ALLOWANCE") !== false) {
                $bags['segment'] = $detail[1];
                if (stripos($val, "NIL/") !== false ||
                    stripos($val, "*/") !== false
                    ) {
                        if (stripos($val, "1STCHECKED") !== false) {
                            $bagsString = explode('1STCHECKED', $val);
                            $detailBag = explode('/', $bagsString[1]);
                            if (stripos($detailBag[0], "USD") !== false) {
                                $bagItem = [
                                    'ordinal' => '1st',
                                    'piece' => 1,
                                    'weight' => 'N/A',
                                    'height' => 'N/A',
                                    'price' => explode('-', $detailBag[0])[2],
                                ];
                                $detailVolume = explode('UP TO', $bagsString[1]);
                                if (isset($detailVolume[1])) {
                                    $bagItem['weight'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[1])));
                                }
                                if (isset($detailVolume[2])) {
                                    $bagItem['height'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[2])));
                                }
                                $bags['paid_baggage'][] = $bagItem;
                            }
                        }
                    } else {

                        $detailBag = explode('/', $detail[2]);
                        $bags['free_baggage'] = [
                            'piece' => (int)str_replace('P', '', $detailBag[0]),
                            'weight' => 'N/A',
                            'height' => 'N/A',
                            'price' => 'USD0'
                        ];
                        $detailVolume = explode('UP TO', $detail[2]);
                        if (isset($detailVolume[1])) {
                            $bags['free_baggage']['weight'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[1])));
                        }
                        if (isset($detailVolume[2])) {
                            $bags['free_baggage']['height'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[2])));
                        }
                    }
            } else {
                $detailBag = explode('/', $detail[2]);
                if (stripos($detailBag[0], "USD") !== false) {
                    $bagItem = [
                        'ordinal' => '2nd',
                        'piece' => 1,
                        'weight' => 'N/A',
                        'height' => 'N/A',
                        'price' => $detailBag[0],
                    ];

                    $detailVolume = explode('UP TO', $detail[2]);
                    if (isset($detailVolume[1])) {
                        $bagItem['weight'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[1])));
                    }
                    if (isset($detailVolume[2])) {
                        $bagItem['height'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[2])));
                    }
                    $bags['paid_baggage'][] = $bagItem;
                }

            }
        }
        return $bags;
    }
}
