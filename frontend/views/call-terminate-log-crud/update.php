<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\callTerminateLog\entity\CallTerminateLog */

$this->title = 'Update Call Terminate Log: ' . $model->ctl_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Terminate Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ctl_id, 'url' => ['view', 'id' => $model->ctl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="call-terminate-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
