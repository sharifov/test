<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\StatusWeight */

$this->title = 'Update Status Weight: ' . $model->getStatusName();
$this->params['breadcrumbs'][] = ['label' => 'Status Weight', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getStatusName(), 'url' => ['view', 'id' => $model->sw_status_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="status-weight-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
