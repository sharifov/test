<?php

namespace sales\entities\cases;

use common\models\Employee;

class CasesStatusTransferList
{
    public const FULL = CasesStatus::STATUS_ROUTE_RULES;

    public const LIMITED = [
        CasesStatus::STATUS_PENDING => [
            CasesStatus::STATUS_PROCESSING,
        ],
        CasesStatus::STATUS_PROCESSING => [
            CasesStatus::STATUS_PROCESSING,
            CasesStatus::STATUS_FOLLOW_UP,
            CasesStatus::STATUS_TRASH,
            CasesStatus::STATUS_SOLVED,
        ],
        CasesStatus::STATUS_FOLLOW_UP => [
            CasesStatus::STATUS_PROCESSING,
        ],
        CasesStatus::STATUS_TRASH => [
            CasesStatus::STATUS_PROCESSING,
        ],
        CasesStatus::STATUS_SOLVED => [
            CasesStatus::STATUS_PROCESSING,
        ],
    ];

    public static function getAllowTransferListByUser(?int $status, Employee $user): array
    {
        if ($user->isAdmin() || $user->isExSuper() || $user->isSupSuper()) {
            $rules = self::FULL;
        } else {
            $rules = self::LIMITED;
        }

        $list = [];
        if (!isset($rules[$status])) {
            return $list;
        }
        foreach ($rules[$status] as $item) {
            $list[$item] = CasesStatus::getName($item);
        }
        return $list;
    }
}
