<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\conference\entity\conferenceRecordingLog\ConferenceRecordingLog */

$this->title = 'Update Conference Recording Log: ' . $model->cfrl_id;
$this->params['breadcrumbs'][] = ['label' => 'Conference Recording Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cfrl_id, 'url' => ['view', 'cfrl_id' => $model->cfrl_id, 'cfrl_year' => $model->cfrl_year, 'cfrl_month' => $model->cfrl_month]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="conference-recording-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
