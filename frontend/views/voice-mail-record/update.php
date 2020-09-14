<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\voiceMailRecord\entity\VoiceMailRecord */

$this->title = 'Update Voice Mail Record: ' . $model->vmr_call_id;
$this->params['breadcrumbs'][] = ['label' => 'Voice Mail Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->vmr_call_id, 'url' => ['view', 'id' => $model->vmr_call_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="voice-mail-record-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
