<?php

namespace sales\helpers\cases;

use common\models\Employee;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use sales\entities\cases\CasesStatusTransferList;
use yii\helpers\Html;

class CasesViewRenderHelper
{
    public static function renderTakeButton(Cases $model, Employee $user): string
    {
        if ($model->isSolved()) {
            return '';
        }
        $allowActionsList = CasesStatusTransferList::getAllowTransferListByUser($model->cs_status, $user);
        if (isset($allowActionsList[CasesStatus::STATUS_PROCESSING]) && !$model->isOwner($user->id)) {
            if ($model->isProcessing()) {
                return Html::a('Take over ', ['cases/take', 'gid' => $model->cs_gid], ['class' => 'btn btn-primary take-processing-btn', 'title' => 'Take over']);
            }
            return Html::a('Take', ['cases/take', 'gid' => $model->cs_gid], ['class' => 'btn btn-primary take-processing-btn', 'title' => 'Take']);
        }
        return '';
    }

    public static function renderChangeStatusButton(int $status, Employee $user): string
    {
        $list =  CasesStatusTransferList::getAllowTransferListByUser($status, $user);
        if (!$user->isAdmin() && !$user->isExSuper() && !$user->isSupSuper()) {
            if (isset($list[CasesStatus::STATUS_PROCESSING])) {
                unset($list[CasesStatus::STATUS_PROCESSING]);
            }
        }
        return $list ? Html::button('<i class="fa fa-exchange"></i> Change Status', ['class' => 'btn btn-warning', 'id' => 'btn-change-status', 'title' => 'Change Case status']) : '';
    }
}
