<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadStatusReasonLog\entity\LeadStatusReasonLog */

$this->title = 'Update Lead Status Reason Log: ' . $model->lsrl_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Status Reason Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lsrl_id, 'url' => ['view', 'lsrl_id' => $model->lsrl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-status-reason-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
