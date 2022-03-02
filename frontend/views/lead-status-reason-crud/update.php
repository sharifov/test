<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadStatusReason\entity\LeadStatusReason */

$this->title = 'Update Lead Status Reason: ' . $model->lsr_name;
$this->params['breadcrumbs'][] = ['label' => 'Lead Status Reasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lsr_id, 'url' => ['view', 'lsr_id' => $model->lsr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-status-reason-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
