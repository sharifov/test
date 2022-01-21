<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\department\DepartmentColumn;
use common\components\grid\UserSelect2Column;
use common\models\Department;
use common\models\Project;
use src\model\emailReviewQueue\entity\EmailReviewQueue;
use src\model\emailReviewQueue\entity\EmailReviewQueueStatus;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\emailReviewQueue\entity\EmailReviewQueueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Email Review Queues';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-review-queue-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Email Review Queue', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'erq_id',
            [
                'attribute' => 'erq_email_id',
                'value' => static function (EmailReviewQueue $model) {
                    return Html::a('<i class="fa fa-link"></i> ' . $model->erq_email_id, ['/email/view', 'id' => $model->erq_email_id], ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'erq_project_id',
                'value' => static function (EmailReviewQueue $model) {
                    return Yii::$app->formatter->asProjectName($model->erqProject);
                },
                'filter' => Project::getList(),
                'format' => 'raw',
            ],
            [
                'class' => DepartmentColumn::class,
                'attribute' => 'erq_department_id',
                'relation' => 'erqDepartment',
                'filter' => Department::getList(),
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'erq_owner_id',
                'relation' => 'erqOwner',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
            [
                'attribute' => 'erq_status_id',
                'value' => static function (EmailReviewQueue $model) {
                    return EmailReviewQueueStatus::asFormat($model->erq_status_id);
                },
                'format' => 'raw',
                'filter' => EmailReviewQueueStatus::getList()
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

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
