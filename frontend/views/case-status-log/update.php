<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\CaseStatusLog */

$this->title = 'Update Case Status Log: ' . $model->csl_id;
$this->params['breadcrumbs'][] = ['label' => 'Cases Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->csl_id, 'url' => ['view', 'id' => $model->csl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="case-status-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
