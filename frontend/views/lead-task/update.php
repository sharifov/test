<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LeadTask */

$this->title = 'Update Lead Task: ' . $model->lt_lead_id.' '.$model->lt_task_id.' '.$model->lt_date;
$this->params['breadcrumbs'][] = ['label' => 'Lead Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lt_lead_id, 'url' => ['view', 'lt_lead_id' => $model->lt_lead_id, 'lt_task_id' => $model->lt_task_id, 'lt_user_id' => $model->lt_user_id, 'lt_date' => $model->lt_date]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-task-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
