<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogQueue\CallLogQueue */

$this->title = 'Update Call Log Queue: ' . $model->clq_cl_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Log Queues', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->clq_cl_id, 'url' => ['view', 'id' => $model->clq_cl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="call-log-queue-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
