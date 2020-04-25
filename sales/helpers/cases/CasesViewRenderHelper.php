<?php

namespace sales\helpers\cases;

use common\models\Employee;
use sales\auth\Auth;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use sales\entities\cases\CasesStatusTransferList;
use yii\helpers\Html;

class CasesViewRenderHelper
{
    public static function renderTakeButton(Cases $case, Employee $user): string
    {
        if ($case->isSolved()) {
            return '';
        }

        if (Auth::can('cases/take', ['case' => $case])) {
            return Html::a('Take', ['/cases/take', 'gid' => $case->cs_gid], ['class' => 'btn btn-primary take-processing-btn', 'title' => 'Take']);
        }
        if (Auth::can('cases/takeOver', ['case' => $case])) {
            return Html::a('Take over', ['/cases/take-over', 'gid' => $case->cs_gid], ['class' => 'btn btn-primary take-processing-btn', 'title' => 'Take over']);
        }

        return '';
    }

    public static function renderChangeStatusButton(int $status, Employee $user): string
    {
        $list = CasesStatusTransferList::getAllowTransferListByUser($status, $user);
        if (!$user->isAdmin() && !$user->isExSuper() && !$user->isSupSuper()) {
            if (isset($list[CasesStatus::STATUS_PROCESSING])) {
                unset($list[CasesStatus::STATUS_PROCESSING]);
            }
        }
        return $list ? Html::button('<i class="fa fa-exchange"></i> Change Status', ['class' => 'btn btn-warning', 'id' => 'btn-change-status', 'title' => 'Change Case status']) : '';
    }

    public static function renderCheckedButton(Cases $case): string
    {
        if ($case->isNeedAction()) {
            return Html::a('Mark Checked', ['/cases/mark-checked', 'gid' => $case->cs_gid], ['class' => 'btn btn-info', 'title' => 'Mark as checked']);
        }
        return Html::tag('span', 'Checked', ['class' => 'badge badge-warning', 'title' => 'Mark as checked', 'style' => 'padding:9px;background-color:#A9A9A9']);
    }
}
