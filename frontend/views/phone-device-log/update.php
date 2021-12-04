<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\voip\phoneDevice\PhoneDeviceLog */

$this->title = 'Update Phone Device Log: ' . $model->pdl_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Device Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pdl_id, 'url' => ['view', 'pdl_id' => $model->pdl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="phone-device-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
