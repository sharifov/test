<?php

use yii\helpers\Html;
use \common\models\Call;

/* @var $this yii\web\View */
/* @var $model \common\models\UserConnection */

$isCallFree = $model->ucUser->isCallFree();
$isCallStatusReady = $model->ucUser->isCallStatusReady();

if($isCallFree && $isCallStatusReady) {
    $class = 'text-success';
} elseif($isCallStatusReady) {
    $class = 'text-warning';
} else {
    $class = 'text-danger';
}

?>

<div class="col-md-6" style="margin-bottom: 5px">
    <?=Html::tag('i', '', ['class' => 'fa fa-user fa-lg '.$class, 'title' => $model->uc_user_id])?>
    <?=Html::encode($model->ucUser->username)?>
</div>
