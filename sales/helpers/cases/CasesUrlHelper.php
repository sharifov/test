<?php

namespace sales\helpers\cases;

use sales\entities\cases\Cases;
use yii\helpers\Html;

class CasesUrlHelper
{
    public static function getUrl(?int $id, ?string $gid): ?string
    {
        if (!$id && !$gid) {
            return null;
        }

        if ($id && $gid) {
            return Html::a($id, \Yii::$app->params['url_address'] . '/cases/view/' . $gid);
        }

        if (!$id) {
            if ($case = Cases::findOne(['cs_gid' => $gid])) {
                return Html::a($case->cs_id, \Yii::$app->params['url_address'] . '/cases/view/' . $case->cs_gid);
            }
            return null;
        }

        if (!$gid) {
            if ($case = Cases::findOne($id)) {
                return Html::a($case->cs_id, \Yii::$app->params['url_address'] . '/cases/view/' . $case->cs_gid);
            }
            return null;
        }

        return null;
    }
}
