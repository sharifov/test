<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\client\notifications\sms\entity\ClientNotificationSmsList */

$this->title = 'Create Client Notification Sms';
$this->params['breadcrumbs'][] = ['label' => 'Client Notification Sms List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-notification-sms-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
