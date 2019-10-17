<?php

use yii\helpers\Html;
use \common\models\Call;

/* @var $this yii\web\View */
/* @var $model \common\models\UserConnection */
/* @var $index int */

$isCallFree = $model->ucUser->isCallFree();
$isCallStatusReady = $model->ucUser->isCallStatusReady();

if($isCallFree && $isCallStatusReady) {
    $class = 'text-success';
} elseif($isCallStatusReady) {
    $class = 'text-warning';
} else {
    $class = 'text-danger';
}

if ($model->ucUser->isAdmin()) {
    $iconClass = 'fa-android';
} elseif ($model->ucUser->isSupervision() || $model->ucUser->isSupSuper() || $model->ucUser->isExSuper()) {
    $iconClass = 'fa-user-md';
} elseif ($model->ucUser->isQa()) {
    $iconClass = 'fa-linux';
} else {
    $iconClass = 'fa-user';
}

?>

<div class="col-md-6" style="margin-bottom: 5px">
    <?=($index + 1)?>.
    <?=Html::tag('i', '', ['class' => 'fa ' . $iconClass . ' fa-lg ' . $class, 'title' => $model->uc_user_id])?>
    <?=Html::encode($model->ucUser->username)?>
</div>
