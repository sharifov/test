<?php

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\grid\columns\QaObjectTypeColumn;
use modules\qaTask\src\grid\columns\QaTaskCreatedTypeColumn;
use modules\qaTask\src\grid\columns\QaTaskQueueActionColumn;
use modules\qaTask\src\grid\columns\QaTaskRatingColumn;
use modules\qaTask\src\grid\columns\QaTaskStatusColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\department\DepartmentColumn;
use sales\yii\grid\project\ProjectColumn;
use sales\yii\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\qaTask\src\entities\qaTask\search\QaTaskCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Qa Tasks Search';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qa-task-search">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            't_id',
            't_gid',
            [
                'class' => ProjectColumn::class,
                'attribute' => 't_project_id',
                'relation' => 'project',
                'filter' => $searchModel->getProjectList(),
            ],
            [
                'class' => QaObjectTypeColumn::class,
                'attribute' => 't_object_type_id',
                'filter' => $searchModel->getObjectTypeList(),
            ],
            't_object_id',
            [
                'attribute' => 't_category_id',
                'value' => static function(QaTask $task) {
                    return $task->t_category_id ? $task->category->tc_name : null;
                },
                'filter' => $searchModel->getCategoryList(),
            ],
            [
                'class' => QaTaskStatusColumn::class,
                'attribute' => 't_status_id',
                'filter' => $searchModel->getStatusList(),
            ],
            [
                'class' => QaTaskRatingColumn::class,
                'attribute' => 't_rating',
                'filter' => $searchModel->getRatingList(),
            ],
            [
                'class' => QaTaskCreatedTypeColumn::class,
                'attribute' => 't_create_type_id',
                'filter' => $searchModel->getCreatedTypeList(),
            ],
            [
                'class' => DepartmentColumn::class,
                'attribute' => 't_department_id',
                'relation' => 'department',
                'filter' => $searchModel->getDepartmentList(),
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 't_assigned_user_id',
                'relation' => 'assignedUser',
                'filter' => $searchModel->getUserList(),
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 't_created_user_id',
                'relation' => 'createdUser',
                'filter' => $searchModel->getUserList(),
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 't_updated_user_id',
                'relation' => 'updatedUser',
                'filter' => $searchModel->getUserList(),
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
