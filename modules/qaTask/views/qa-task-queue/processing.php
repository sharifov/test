<?php

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTaskCategory\QaTaskCategoryQuery;
use modules\qaTask\src\grid\columns\QaObjectTypeColumn;
use modules\qaTask\src\grid\columns\QaTaskCreatedTypeColumn;
use modules\qaTask\src\grid\columns\QaTaskQueueActionColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\department\DepartmentColumn;
use sales\yii\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\qaTask\src\entities\qaTask\search\queue\QaTaskQueuePendingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Qa Tasks Processing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qa-task-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            't_id',
            't_gid',
            [
                'class' => QaObjectTypeColumn::class,
                'attribute' => 't_object_type_id',
            ],
            't_object_id',
            [
                'attribute' => 't_category_id',
                'value' => static function(QaTask $task) {
                    return $task->t_category_id ? $task->category->tc_name : null;
                },
                'filter' => QaTaskCategoryQuery::getList(),
            ],
            't_rating',
            [
                'class' => QaTaskCreatedTypeColumn::class,
                'attribute' => 't_create_type_id',
            ],
            [
                'class' => DepartmentColumn::class,
                'attribute' => 't_department_id',
                'relation' => 'department',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 't_assigned_user_id',
                'relation' => 'assignedUser',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 't_created_user_id',
                'relation' => 'createdUser',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 't_updated_user_id',
                'relation' => 'updatedUser',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 't_deadline_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 't_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 't_updated_dt',
            ],
            [
                'class' => QaTaskQueueActionColumn::class,
            ],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
