<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\voiceMailRecord\entity\VoiceMailRecord */

$this->title = 'Create Voice Mail Record';
$this->params['breadcrumbs'][] = ['label' => 'Voice Mail Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="voice-mail-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
