<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LeadTask */

$this->title = $model->lt_lead_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-task-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'lt_lead_id' => $model->lt_lead_id, 'lt_task_id' => $model->lt_task_id, 'lt_user_id' => $model->lt_user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'lt_lead_id' => $model->lt_lead_id, 'lt_task_id' => $model->lt_task_id, 'lt_user_id' => $model->lt_user_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'lt_lead_id',
            'lt_task_id',
            'lt_user_id',
            'lt_date',
            'lt_notes',
            'lt_completed_dt',
            'lt_updated_dt',
        ],
    ]) ?>

</div>
