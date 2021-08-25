<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\client\notifications\phone\entity\ClientNotificationPhoneList */

$this->title = 'Create Client Notification Phone List';
$this->params['breadcrumbs'][] = ['label' => 'Client Notification Phone Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-notification-phone-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
