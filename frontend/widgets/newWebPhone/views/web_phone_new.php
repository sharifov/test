<?php

use yii\bootstrap4\Modal;
use yii\web\View;

/* @var $formattedPhoneProject string */
/* @var $userCallStatus \common\models\UserCallStatus */
/* @var $this View */
/** @var array $userPhones */
/** @var array $userEmails */
/** @var int $countMissedCalls */

\frontend\widgets\newWebPhone\NewWebPhoneAsset::register($this);

Modal::begin([
    'id' => 'call-box-modal',
    'title' => '',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
    'size' => Modal::SIZE_LARGE
]);

Modal::end();

Modal::begin([
    'id' => 'web-phone-redirect-agents-modal',
    'title' => 'Transfer Call',
    //'size' => 'modal-sm',
]);
Modal::end();

?>

<?= $this->render('partial/_phone_widget', [
    'userPhones' => $userPhones,
    'userEmails' => $userEmails,
    'userCallStatus' => $userCallStatus,
    'countMissedCalls' => $countMissedCalls,
    'formattedPhoneProject' => $formattedPhoneProject,
]) ?>

<?= $this->render('partial/_phone_widget_icon') ?>

<?php
