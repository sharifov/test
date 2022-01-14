<?php

namespace src\model\leadRequest\service;

/**
 * Class LeadRequestService
 */
class LeadRequestService
{
    public static function findByColumnId(string $columnId, array $userColumnData): ?string
    {
        foreach ($userColumnData as $value) {
            if ($value['column_id'] === $columnId) {
                return $value['string_value'];
            }
        }
        return null;
    }
}
