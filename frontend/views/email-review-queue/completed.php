<?php

/* @var $this yii\web\View */
/* @var $searchModel EmailReviewQueueSearch */
/* @var $dataProvider ActiveDataProvider */

use common\components\grid\DateTimeColumn;
use common\components\grid\department\DepartmentColumn;
use common\components\grid\UserSelect2Column;
use common\models\Department;
use frontend\themes\gentelella_v2\widgets\FlashAlert;
use src\auth\Auth;
use src\model\emailReviewQueue\entity\EmailReviewQueue;
use src\model\emailReviewQueue\entity\EmailReviewQueueSearch;
use src\model\emailReviewQueue\entity\EmailReviewQueueStatus;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Email Review Queue';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="email-review-queue-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'erq_id',
                'options' => [
                    'width' => '80px'
                ]
            ],
            [
                'attribute' => 'erq_email_id',
                'value' => static function (EmailReviewQueue $model) {
                    $url = ($model->erq_email_is_norm) ? '/email-normalized/view' : '/email/view';
                    return Html::a('<i class="fa fa-link"></i> ' . $model->erq_email_id, [$url, 'id' => $model->erq_email_id], ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw',
                'options' => [
                    'width' => '110px'
                ]
            ],
            [
                'attribute' => 'erq_project_id',
                'value' => static function (EmailReviewQueue $model) {
                    return Yii::$app->formatter->asProjectName($model->erqProject);
                },
                'filter' => \common\models\Project::getList(),
                'format' => 'raw',
            ],
            [
                'class' => DepartmentColumn::class,
                'attribute' => 'erq_department_id',
                'relation' => 'erqDepartment',
                'filter' => Department::getList(),
                'format' => 'departmentName'
            ],
            'emailSubject',
            'emailTemplateName',
            'emailLead:lead',
            'emailCase:case',
            'emailStatusName',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'erq_owner_id',
                'relation' => 'erqOwner',
                'format' => 'username',
                'placeholder' => 'Select User',
                'label' => 'Email Creator'
            ],
            [
                'attribute' => 'erq_status_id',
                'value' => static function (EmailReviewQueue $model) {
                    return EmailReviewQueueStatus::asFormat($model->erq_status_id);
                },
                'format' => 'raw',
                'filter' => EmailReviewQueueStatus::getCompletedList(),
                'label' => 'Review Status'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'erq_user_reviewer_id',
                'relation' => 'erqUserReviewer',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
            ['class' => DateTimeColumn::class, 'attribute' => 'erq_created_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'erq_updated_dt'],
            [
                'class' => ActionColumn::class,
                'template' => '{view}',
                'buttons' => [
                    'view' => static function ($url, EmailReviewQueue $model) {
                        return Html::a('View', ['/email-review-queue/review', 'id' => $model->erq_id, 'view' => true], [
                            'class' => 'btn btn-warning btn-xs',
                            'data-pjax' => 0,
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
