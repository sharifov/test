<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskHelper;
use modules\taskList\src\entities\userTask\UserTaskStatusLog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\taskList\src\entities\userTask\UserTaskStatusLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Task Status Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-task-status-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Task Status Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'utsl_id',
            'utsl_ut_id',
            'utsl_description:html',
            [
                'attribute' => 'utsl_old_status',
                'value' => static function (UserTaskStatusLog $model) {
                    return UserTaskHelper::statusLabel($model->utsl_old_status);
                },
                'format' => 'raw',
                'filter' => UserTask::STATUS_LIST,
            ],
            [
                'attribute' => 'utsl_new_status',
                'value' => static function (UserTaskStatusLog $model) {
                    return UserTaskHelper::statusLabel($model->utsl_new_status);
                },
                'format' => 'raw',
                'filter' => UserTask::STATUS_LIST,
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'utsl_created_user_id',
                'relation' => 'utslCreatedUser',
                'placeholder' => 'Employee',
                'format' => 'userNameWithId',
            ],
            [
                'class' => DateTimeColumn::class,
                'limitEndDay' => false,
                'attribute' => 'utsl_created_dt',
                'format' => 'byUserDateTimeAndUTC',
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, UserTaskStatusLog $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'utsl_id' => $model->utsl_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
