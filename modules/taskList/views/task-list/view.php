<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\taskList\TaskList */

$this->title = $model->tl_id;
$this->params['breadcrumbs'][] = ['label' => 'Task Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="task-list-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'tl_id' => $model->tl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'tl_id' => $model->tl_id], [
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
            'tl_id',
            'tl_title',
            'tl_object',
            'tl_condition',
            'tl_condition_json',
            'tl_params_json',
            'tl_work_start_time_utc',
            'tl_work_end_time_utc',
            'tl_duration_min',
            'tl_enable_type',
            'tl_cron_expression',
            'tl_sort_order',
            'tl_updated_dt',
            'tl_updated_user_id',
        ],
    ]) ?>

</div>
