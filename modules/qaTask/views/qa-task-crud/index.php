<?php

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\grid\columns\QaTaskObjectTypeColumn;
use modules\qaTask\src\grid\columns\QaTaskCreatedTypeColumn;
use modules\qaTask\src\grid\columns\QaTaskRatingColumn;
use modules\qaTask\src\grid\columns\QaTaskStatusColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\department\DepartmentColumn;
use common\components\grid\project\ProjectColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\qaTask\src\entities\qaTask\search\QaTaskCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Qa Tasks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qa-task-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Qa Task', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

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
                'class' => QaTaskObjectTypeColumn::class,
                'attribute' => 't_object_type_id',
                'filter' => $searchModel->getObjectTypeList(),
            ],
            't_object_id',
            [
                'attribute' => 't_category_id',
                'value' => static function (QaTask $task) {
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
                'class' => UserSelect2Column::class,
                'attribute' => 't_assigned_user_id',
                'relation' => 'assignedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 't_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 't_updated_user_id',
                'relation' => 'updatedUser',
                'placeholder' => 'Select User',
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
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
