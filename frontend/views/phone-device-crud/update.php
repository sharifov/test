<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\voip\phoneDevice\device\PhoneDevice */

$this->title = 'Update Phone Device: ' . $model->pd_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Devices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pd_id, 'url' => ['view', 'id' => $model->pd_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="phone-device-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
