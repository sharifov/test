<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog */

$this->title = 'Update Lead Poor Processing Log: ' . $model->lppl_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Poor Processing Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lppl_id, 'url' => ['view', 'lppl_id' => $model->lppl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-poor-processing-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
