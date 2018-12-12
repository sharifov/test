<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SmsTemplateType */

$this->title = 'Update Sms Template Type: ' . $model->stp_id;
$this->params['breadcrumbs'][] = ['label' => 'Sms Template Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->stp_id, 'url' => ['view', 'id' => $model->stp_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sms-template-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
