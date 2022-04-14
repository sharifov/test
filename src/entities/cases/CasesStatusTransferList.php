<?php

namespace src\entities\cases;

use common\models\Employee;
use modules\cases\src\abac\dto\CasesAbacDto;
use modules\cases\src\abac\CasesAbacObject;
use Yii;

class CasesStatusTransferList
{
    public const ORIGIN = CasesStatus::STATUS_LIST;
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

    public static function getAllowTransferListByAbac(Cases $case): array
    {
        $list = self::ORIGIN;

        foreach ($list as $statusId => $status) {
            $caseAbacDto = new CasesAbacDto($case, $statusId);
            $caseAbacDto->pqc_status = $case->productQuoteChange->pqc_status_id ?? null;
            $caseAbacDto->pqr_status = $case->productQuoteRefund->pqr_status_id ?? null;

            /** @abac new $caseAbacDto, CasesAbacObject::OBJ_CASE_STATUS_ROUTE_RULES, CasesAbacObject::ACTION_TRANSFER, Case Status transfer rules */
            $isConsoleApp = (Yii::$app instanceof \yii\console\Application);
            if (!$isConsoleApp && !Yii::$app->abac->can($caseAbacDto, CasesAbacObject::OBJ_CASE_STATUS_ROUTE_RULES, CasesAbacObject::ACTION_TRANSFER)) {
                unset($list[$statusId]);
            }
        }

        return $list;
    }
}
