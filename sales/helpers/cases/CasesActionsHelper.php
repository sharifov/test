<?php

namespace sales\helpers\cases;

use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use yii\helpers\Html;
use Yii;

class CasesActionsHelper
{

    /**
     * @param Cases $model
     * @param $userId
     * @return string
     */
    public static function renderTakeButton(Cases $model, $userId): string
    {
        $allowActionsList = CasesStatus::getAllowList($model->cs_status);
        if (isset($allowActionsList[CasesStatus::STATUS_PROCESSING]) && !$model->isOwner($userId)) {
            if ($model->isProcessing()) {
                return Html::a('Take over ', ['cases/take-over', 'gid' => $model->cs_gid, 'uid' => Yii::$app->user->id], ['class' => 'btn btn-primary take-processing-btn', 'title' => 'Take over']);
            } else {
                return Html::a('Take', ['cases/take', 'gid' => $model->cs_gid, 'uid' => Yii::$app->user->id], ['class' => 'btn btn-primary take-processing-btn', 'title' => 'Take']);
            }
        }
        return '';
    }
}