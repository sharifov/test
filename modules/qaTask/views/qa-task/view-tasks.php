<?php

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\search\object\QaTaskObjectSearch;
use modules\qaTask\src\grid\columns\QaTaskRatingColumn;
use modules\qaTask\src\grid\columns\QaTaskStatusColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel QaTaskObjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qa-task-view-tasks">

    <?php Pjax::begin([
        'enableReplaceState' => false,
        'enablePushState' => false,
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            't_id',
            [
                'class' => QaTaskStatusColumn::class,
                'attribute' => 't_status_id',
                'filter' => $searchModel->getStatusList(),
            ],
            [
                'attribute' => 't_category_id',
                'value' => static function(QaTask $task) {
                    return $task->t_category_id ? $task->category->tc_name : null;
                },
                'filter' => $searchModel->getCategoryList(),
            ],
            [
                'class' => QaTaskRatingColumn::class,
                'attribute' => 't_rating',
                'filter' => $searchModel->getRatingList(),
            ],
            't_description',
            [
                'class' => UserColumn::class,
                'attribute' => 't_assigned_user_id',
                'relation' => 'assignedUser',
                'filter' => $searchModel->getUserList(),
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 't_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 't_updated_dt',
            ],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
