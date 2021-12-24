<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\smsSubscribe\entity\SmsSubscribe */

$this->title = 'Update Sms Subscribe: ' . $model->ss_id;
$this->params['breadcrumbs'][] = ['label' => 'Sms Subscribes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ss_id, 'url' => ['view', 'id' => $model->ss_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sms-subscribe-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
