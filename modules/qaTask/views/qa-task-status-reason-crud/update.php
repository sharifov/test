<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskStatusReason\QaTaskStatusReason */

$this->title = 'Update Qa Task Status Reason: ' . $model->tsr_id;
$this->params['breadcrumbs'][] = ['label' => 'Qa Task Status Reasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->tsr_id, 'url' => ['view', 'id' => $model->tsr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="qa-task-status-reason-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
