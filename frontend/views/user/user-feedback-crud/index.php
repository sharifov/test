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
use common\components\grid\UserSelect2Column;

/* @var $this yii\web\View */
/* @var $searchModel modules\user\userFeedback\entity\search\UserFeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Feedbacks';
$this->params['breadcrumbs'][] = $this->title;

$userAbacDto = new UserAbacDto('username');

$columns = [
    'uf_id',
    [
        'label' => 'Type',
        'attribute' => 'uf_type_id',
        'value' => static function (UserFeedback $model) {
            return $model->getTypeLabel();
        },
        'format' => 'raw',
        'filter' => UserFeedback::getTypeList()
    ],
    [
        'label' => 'Status',
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
            $message = StringHelper::truncate($model->uf_message, 400, '...', null, true);
            return '<pre><small>' .
                \frontend\widgets\ShowMoreFieldWidget::addLinkToShowMore($message, $model->uf_message, $model->uf_id) .
                '</small></pre>';
        },
        'format' => 'raw'
    ],
    ['class' => DateTimeColumn::class, 'attribute' => 'uf_created_dt'],
    [
        'class' => UserSelect2Column::class,
        'attribute' => 'uf_created_user_id',
        'relation' => 'ufCreatedUser',
        'placeholder' => 'Select user'
    ],
    [
        'attribute' => 'uf_resolution',
        'value' => static function (UserFeedback $model) {
            if (!$model->uf_resolution) {
                return null;
            }
            return '<pre><small>' . (StringHelper::truncate($model->uf_resolution, 400, '...', null, true)) . '</small></pre>';
        },
        'format' => 'raw'
    ],
    [
        'class' => UserSelect2Column::class,
        'attribute' => 'uf_resolution_user_id',
        'relation' => 'ufResolutionUser',
        'placeholder' => 'Select user'
    ],
    ['class' => DateTimeColumn::class, 'attribute' => 'uf_resolution_dt'],
    [
        'class' => ActionColumn::class,
        'template' => '{view} {update} {delete} {resolve}',
        'urlCreator' => static function ($action, UserFeedback $model, $key, $index, $column) {
            return Url::toRoute([$action, 'uf_id' => $model->uf_id, 'uf_created_dt' => $model->uf_created_dt]);
        },
        'buttons' => [
            'resolve' => function ($action, $model, $key) {
                $url = Url::toRoute([$action, 'uf_id' => $model->uf_id, 'uf_created_dt' => $model->uf_created_dt]);
                return Html::a('<i class="fa fa-check"></i>', $url, ['title' => 'Resolve']);
            },
        ]
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
<?= \frontend\widgets\ShowMoreFieldWidget::widget(['title' => 'Message - User Feedback']); ?>

