<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\CaseEventLog */

$this->title = 'Update Case Event Log: ' . $model->cel_id;
$this->params['breadcrumbs'][] = ['label' => 'Case Event Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cel_id, 'url' => ['view', 'id' => $model->cel_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="case-event-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
