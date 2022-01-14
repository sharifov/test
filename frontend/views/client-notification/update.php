<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\client\notifications\client\entity\ClientNotification */

$this->title = 'Update Client Notification: ' . $model->cn_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Notifications', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cn_id, 'url' => ['view', 'id' => $model->cn_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-notification-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
