<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\callTerminateLog\entity\CallTerminateLog */

$this->title = 'Create Call Terminate Log';
$this->params['breadcrumbs'][] = ['label' => 'Call Terminate Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-terminate-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
