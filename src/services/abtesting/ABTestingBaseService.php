<?php

namespace src\services\abtesting;

abstract class ABTestingBaseService
{
    protected function getExpectedKey(ABTestingBaseEntity ...$items): string
    {
        $totalCounter = 0;
        foreach ($items as $item) {
            $totalCounter += $item->getCounter();
        }
        foreach ($items as $key => $item) {
            $items[$key]->calculateCurrentPercentage($totalCounter);
        }
        $percentageDiffArray = [];
        foreach ($items as $item) {
            $percentageDiffArray[$item->getName()] = $item->getExpectedPercentage() - $item->getCurrentPercentage();
        }
        return $this->getExpectedKeyFromPercentageDiffArray($percentageDiffArray);
    }


    protected function getExpectedKeyFromPercentageDiffArray(array $percentageDiffArray): string
    {
        arsort($percentageDiffArray);
        $reversedArray       = array_reverse($percentageDiffArray);
        $maxDifference    = array_pop($reversedArray);
        $resultArray = [];
        foreach ($percentageDiffArray as $key => $value) {
            if ($maxDifference === $value) {
                $resultArray[] = $key;
            } else {
                break;
            }
        }
        $count = count($resultArray);
        if ($count > 1) {
            return $resultArray[random_int(0, $count - 1)];
        }
        return $resultArray[0];
    }
}
