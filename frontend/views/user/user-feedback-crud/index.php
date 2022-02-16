<?php

use kartik\grid\CheckboxColumn;
use common\components\grid\DateTimeColumn;
use common\models\Employee;
use frontend\widgets\multipleUpdate\button\MultipleUpdateButtonWidget;
use kartik\grid\GridView;
use modules\user\src\abac\dto\UserAbacDto;
use modules\user\src\abac\UserAbacObject;
use modules\user\userFeedback\entity\UserFeedback;
use modules\user\userFeedback\entity\UserFeedbackFile;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\user\userFeedback\entity\search\UserFeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Feedbacks';
$this->params['breadcrumbs'][] = $this->title;

$userAbacDto = new UserAbacDto('username');

$columns = [
    'uf_id',
    [
        'attribute' => 'uf_type_id',
        'value' => static function (UserFeedback $model) {
            return $model->getTypeLabel();
        },
        'format' => 'raw',
        'filter' => UserFeedback::getTypeList()
    ],
    [
        'attribute' => 'uf_status_id',
        'value' => static function (UserFeedback $model) {
            return $model->getStatusLabel();
        },
        'format' => 'raw',
        'filter' => UserFeedback::getStatusList()
    ],
    'uf_title',
    [
        'value' => static function (UserFeedback $model) {
            return UserFeedbackFile::find()->andWhere(['uff_uf_id' => $model->uf_id])->count();
        },
        'label' => 'Attached files',
    ],
    [
        'attribute' => 'uf_message',
        'value' => static function (UserFeedback $model) {
            if (!$model->uf_message) {
                return null;
            }
            return '<pre><small>' . (StringHelper::truncate($model->uf_message, 400, '...', null, true)) . '</small></pre>';
        },
        'format' => 'raw'
    ],
    ['class' => DateTimeColumn::class, 'attribute' => 'uf_created_dt'],
    [
        'attribute' => 'uf_created_user_id',
        'value' => static function (UserFeedback $userFeedback) {
            $user = Employee::findOne($userFeedback->uf_created_user_id);
            return $user->username ?? null;
        }
    ],
    [
        'class' => ActionColumn::class,
        'urlCreator' => static function ($action, UserFeedback $model, $key, $index, $column) {
            return Url::toRoute([$action, 'uf_id' => $model->uf_id, 'uf_created_dt' => $model->uf_created_dt]);
        }
    ],
];
?>
<div class="user-feedback-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Feedback', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'feedback-pjax-list']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php $gridId = 'feedback-grid-id'; ?>

    <?php
        /** @abac new $userAbacDto, UserAbacObject::USER_FEEDBACK, UserAbacObject::ACTION_MULTIPLE_UPDATE, Username field view*/
    if (Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FEEDBACK, UserAbacObject::ACTION_MULTIPLE_UPDATE)) :
        ?>

        <?php array_unshift($columns, [
        'class' => CheckboxColumn::class,
        'name' => 'FeedbackMultipleForm[feedback_list]',
        'pageSummary' => true,
        'rowSelectedClass' => GridView::TYPE_INFO,
]); ?>

        <?= MultipleUpdateButtonWidget::widget([
        'modalId' => 'modal-df',
        'showUrl' => Url::to(['/user-feedback-crud/multiple-update-show']),
        'gridId' => $gridId,
        'buttonClass' => 'multiple-update-btn',
        'buttonClassAdditional' => 'btn btn-info btn-warning',
        'buttonText' => 'Multiple update',
]) ?>

    <?php endif; ?>

    <?= GridView::widget([
        'id' => $gridId,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
    ]); ?>

    <?php Pjax::end(); ?>

</div>
