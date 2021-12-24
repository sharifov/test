<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\smsSubscribe\entity\SmsSubscribe */

$this->title = 'Create Sms Subscribe';
$this->params['breadcrumbs'][] = ['label' => 'Sms Subscribes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-subscribe-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
