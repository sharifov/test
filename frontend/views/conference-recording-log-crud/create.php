<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\conference\entity\conferenceRecordingLog\ConferenceRecordingLog */

$this->title = 'Create Conference Recording Log';
$this->params['breadcrumbs'][] = ['label' => 'Conference Recording Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conference-recording-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
