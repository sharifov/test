<?php

use common\models\Lead;
use frontend\widgets\redial\LeadRedialWidget;
use frontend\widgets\redial\RedialUrl;
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
        Url::to(['lead-redial/reservation-from-last-call']),
        'post',
        ['gid' => $lead->gid]
    ),
    'phoneNumberFromUrl' => new RedialUrl(
        Url::to(['lead-redial/phone-number-from']),
        'post',
        ['gid' => $lead->gid]
    ),
    'checkBlackPhoneUrl' => new RedialUrl(
        Url::to(['lead-redial/check-black-phone']),
        'post'
    ),
    'script' => 'reloadCallFunction();',
]);
