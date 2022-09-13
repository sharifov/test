<?php

namespace src\helpers\nestedSets;

class NestedSetsHelper
{
    /**
     * @param array $parentsNames
     * @param string $delimiter
     * @return string
     */
    public static function formatHierarchyString(array $parentsNames, string $delimiter = ' / '): string
    {
        $parentsNamesString = implode($delimiter, $parentsNames);

        return $parentsNamesString;
    }
}
