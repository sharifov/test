<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\twilio\src\entities\voiceLog\VoiceLog */

$this->title = 'Update Voice Log: ' . $model->vl_id;
$this->params['breadcrumbs'][] = ['label' => 'Voice Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->vl_id, 'url' => ['view', 'id' => $model->vl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="voice-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
