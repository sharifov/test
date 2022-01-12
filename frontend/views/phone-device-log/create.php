<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\voip\phoneDevice\log\PhoneDeviceLog */

$this->title = 'Create Phone Device Log';
$this->params['breadcrumbs'][] = ['label' => 'Phone Device Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-device-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
