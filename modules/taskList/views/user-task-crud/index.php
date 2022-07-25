<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\taskList\src\entities\userTask\UserTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Tasks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-task-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Task', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-user-task-index', 'timeout' => 5000, 'enablePushState' => true]); ?>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'ut_id',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ut_user_id',
                'relation' => 'user',
                'placeholder' => 'Employee',
                'format' => 'userNameWithId',
            ],
            [
                'attribute' => 'ut_target_object',
                'value' => static function (UserTask $model) {
                    if (!$model->ut_target_object) {
                        return Yii::$app->formatter->nullDisplay;
                    }
                    return $model->ut_target_object;
                },
                'filter' => TargetObject::TARGET_OBJ_LIST,
                'format' => 'raw',
            ],
            'ut_target_object_id',
            [
                'attribute' => 'ut_task_list_id',
                'value' => static function (UserTask $model) {
                    $taskList = $model->taskList;

                    if (!$taskList) {
                        return Yii::$app->formatter->nullDisplay;
                    }

                    return Html::a(
                        $model->taskList->tl_title . ' (' . $model->ut_task_list_id . ')' ?? '-',
                        [
                            'task-list/view',
                            'tl_id' => $model->ut_task_list_id
                        ],
                        ['target' => '_blank', 'data-pjax' => 0]
                    );
                },
                'format' => 'raw',
            ],
            [
                'class' => DateTimeColumn::class,
                'limitEndDay' => false,
                'attribute' => 'ut_start_dt',
                'format' => 'byUserDateTimeAndUTC',
            ],
            [
                'class' => DateTimeColumn::class,
                'limitEndDay' => false,
                'attribute' => 'ut_end_dt',
                'format' => 'byUserDateTimeAndUTC',
            ],
            [
                'attribute' => 'ut_priority',
                'value' => static function (UserTask $model) {
                    return UserTaskHelper::priorityLabel($model->ut_priority);
                },
                'format' => 'raw',
                'filter' => UserTask::PRIORITY_LIST,
            ],
            [
                'attribute' => 'ut_status_id',
                'value' => static function (UserTask $model) {
                    return UserTaskHelper::statusLabel($model->ut_status_id);
                },
                'format' => 'raw',
                'filter' => UserTask::STATUS_LIST,
            ],
            [
                'class' => DateTimeColumn::class,
                'limitEndDay' => false,
                'attribute' => 'ut_created_dt',
                'format' => 'byUserDateTimeAndUTC',
            ],
            //'ut_year',
            //'ut_month',
            [
                'class' => ActionColumn::class,
                'urlCreator' => static function ($action, UserTask $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'ut_id' => $model->ut_id, 'ut_year' => $model->ut_year, 'ut_month' => $model->ut_month]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
