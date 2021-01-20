<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\callRecordingLog\entity\CallRecordingLog */

$this->title = 'Update Call Recording Log: ' . $model->crl_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Recording Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->crl_id, 'url' => ['view', 'crl_id' => $model->crl_id, 'crl_year' => $model->crl_year, 'crl_month' => $model->crl_month]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="call-recording-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
