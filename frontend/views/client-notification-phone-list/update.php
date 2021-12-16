<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\client\notifications\phone\entity\ClientNotificationPhoneList */

$this->title = 'Update Client Notification Phone: ' . $model->cnfl_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Notification Phone Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cnfl_id, 'url' => ['view', 'id' => $model->cnfl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-notification-phone-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
