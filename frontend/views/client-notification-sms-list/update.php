<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\client\notifications\sms\entity\ClientNotificationSmsList */

$this->title = 'Update Client Notification Sms: ' . $model->cnsl_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Notification Sms List', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cnsl_id, 'url' => ['view', 'id' => $model->cnsl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-notification-sms-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
