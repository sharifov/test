<?php

use modules\taskList\src\entities\taskList\TaskList;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\taskList\TaskList */

$this->title = 'Task List: ' . $model->tl_id;
$this->params['breadcrumbs'][] = ['label' => 'Task Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="task-list-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-edit"></i> Update', ['update', 'tl_id' => $model->tl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'tl_id' => $model->tl_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'tl_id',
            'tl_title',
            'tl_object',
            [
                'attribute' => 'tl_target_object_id',
                'value' => static function (TaskList $model) {
                    return $model->getTargetObjectName();
                }
            ],
            'tl_condition',
//            'tl_condition_json',
//            'tl_params_json',
//            'tl_work_start_time_utc',
//            'tl_work_end_time_utc',
            'tl_duration_min',
            //'tl_enable_type',
            [
                'attribute' => 'tl_enable_type',
                'value' => static function (TaskList $model) {
                    return $model->getEnableTypeLabel();
                },
                'format' => 'raw',
            ],
            'tl_cron_expression',
            'tl_sort_order',
            'tl_updated_dt:datetime',
            'tl_updated_user_id:username',
        ],
    ]) ?>
    </div>
    <div class="col-md-6">
        <h4>Params:</h4>
        <pre><?php \yii\helpers\VarDumper::dump($model->tl_params_json, 10, true) ?></pre>
    </div>

</div>
