<?php

use common\models\Lead;
use frontend\widgets\redial\LeadRedialWidget;
use frontend\widgets\redial\RedialUrl;
use frontend\widgets\redial\ViewUrl;
use yii\helpers\Url;

/** @var Lead $lead */

echo LeadRedialWidget::widget([
    'lead' => $lead,
    'viewUrl' => new RedialUrl(
        Url::to(['lead-redial/show']),
        'post',
        ['gid' => $lead->gid]
    ),
    'takeUrl' => new RedialUrl(
        Url::to(['lead-redial/take']),
        'post',
        ['gid' => $lead->gid]
    ),
    'reservationUrl' => new RedialUrl(
        Url::to(['lead-redial/reservation']),
        'post',
        ['gid' => $lead->gid]
    ),
    'pjaxListContainerId' => 'lead-redial-pjax',
]);
