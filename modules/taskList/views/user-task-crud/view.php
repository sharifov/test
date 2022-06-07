<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\userTask\UserTask */

$this->title = $model->ut_id;
$this->params['breadcrumbs'][] = ['label' => 'User Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-task-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'ut_id' => $model->ut_id, 'ut_year' => $model->ut_year, 'ut_month' => $model->ut_month], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'ut_id' => $model->ut_id, 'ut_year' => $model->ut_year, 'ut_month' => $model->ut_month], [
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
                'ut_id',
                'ut_user_id',
                'ut_target_object',
                'ut_target_object_id',
                'ut_task_list_id',
                'ut_start_dt',
                'ut_end_dt',
                'ut_priority',
                'ut_status_id',
                'ut_created_dt',
                'ut_year',
                'ut_month',
            ],
        ]) ?>

    </div>

</div>
