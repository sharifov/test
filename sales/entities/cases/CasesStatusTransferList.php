<?php

namespace sales\entities\cases;

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

    public static function getAllowTransferListByAbac(Cases $case)
    {
        $list = self::ORIGIN;

        /*if (!Yii::$app->abac->can(new CasesAbacDto($case), CasesAbacObject::OBJ_CASE_IN_PENDING, CasesAbacObject::ACTION_TRANSFER)) {
            unset($list[CasesStatus::STATUS_PENDING]);
        }
        if (!Yii::$app->abac->can(new CasesAbacDto($case), CasesAbacObject::OBJ_CASE_IN_PROCESSING, CasesAbacObject::ACTION_TRANSFER)) {
            unset($list[CasesStatus::STATUS_PROCESSING]);
        }
        if (!Yii::$app->abac->can(new CasesAbacDto($case), CasesAbacObject::OBJ_CASE_IN_FOLLOW_UP, CasesAbacObject::ACTION_TRANSFER)) {
            unset($list[CasesStatus::STATUS_FOLLOW_UP]);
        }
        if (!Yii::$app->abac->can(new CasesAbacDto($case), CasesAbacObject::OBJ_CASE_IN_SOLVED, CasesAbacObject::ACTION_TRANSFER)) {
            unset($list[CasesStatus::STATUS_SOLVED]);
        }
        if (!Yii::$app->abac->can(new CasesAbacDto($case), CasesAbacObject::OBJ_CASE_IN_TRASH, CasesAbacObject::ACTION_TRANSFER)) {
            unset($list[CasesStatus::STATUS_TRASH]);
        }
        if (!Yii::$app->abac->can(new CasesAbacDto($case), CasesAbacObject::OBJ_CASE_IN_NEW, CasesAbacObject::ACTION_TRANSFER)) {
            unset($list[CasesStatus::STATUS_NEW]);
        }
        if (!Yii::$app->abac->can(new CasesAbacDto($case), CasesAbacObject::OBJ_CASE_IN_AWAITING, CasesAbacObject::ACTION_TRANSFER)) {
            unset($list[CasesStatus::STATUS_AWAITING]);
        }
        if (!Yii::$app->abac->can(new CasesAbacDto($case), CasesAbacObject::OBJ_CASE_IN_AUTO_PROCESSING, CasesAbacObject::ACTION_TRANSFER)) {
            unset($list[CasesStatus::STATUS_AUTO_PROCESSING]);
        }
        if (!Yii::$app->abac->can(new CasesAbacDto($case), CasesAbacObject::OBJ_CASE_IN_ERROR, CasesAbacObject::ACTION_TRANSFER)) {
            unset($list[CasesStatus::STATUS_ERROR]);
        }*/

        foreach ($list as $statusId => $status) {
            if (!Yii::$app->abac->can(new CasesAbacDto($case, $statusId), CasesAbacObject::OBJ_CASE_STATUS_ROUTE_RULES, CasesAbacObject::ACTION_TRANSFER)) {
                unset($list[$statusId]);
            }
        }

        return $list;
    }
}
