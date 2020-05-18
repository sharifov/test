<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\twilio\src\entities\voiceLog\VoiceLog */

$this->title = 'Create Voice Log';
$this->params['breadcrumbs'][] = ['label' => 'Voice Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="voice-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
