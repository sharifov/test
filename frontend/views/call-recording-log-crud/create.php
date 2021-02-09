<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\callRecordingLog\entity\CallRecordingLog */

$this->title = 'Create Call Recording Log';
$this->params['breadcrumbs'][] = ['label' => 'Call Recording Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-recording-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
