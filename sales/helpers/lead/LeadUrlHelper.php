<?php

namespace sales\helpers\lead;

use common\models\Lead;
use yii\helpers\Html;

class LeadUrlHelper
{
    public static function getUrl(?int $id, ?string $gid): ?string
    {
        if (!$id && !$gid) {
            return null;
        }

        if ($id && $gid) {
            return Html::a($id, \Yii::$app->params['url_address'] . '/lead/view/' . $gid);
        }

        if (!$id) {
            if ($lead = Lead::findOne(['gid' => $gid])) {
                return Html::a($lead->id, \Yii::$app->params['url_address'] . '/lead/view/' . $lead->gid);
            }
            return null;
        }

        if (!$gid) {
            if ($lead = Lead::findOne($id)) {
                return Html::a($lead->id, \Yii::$app->params['url_address'] . '/lead/view/' . $lead->gid);
            }
            return null;
        }

        return null;
    }
}
