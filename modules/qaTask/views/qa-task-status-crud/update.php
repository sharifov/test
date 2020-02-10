<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus */

$this->title = 'Update Qa Task Status: ' . $model->ts_id;
$this->params['breadcrumbs'][] = ['label' => 'Qa Task Statuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ts_id, 'url' => ['view', 'id' => $model->ts_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="qa-task-status-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
