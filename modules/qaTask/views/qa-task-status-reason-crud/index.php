<?php

use modules\qaTask\src\grid\columns\QaObjectTypeColumn;
use sales\yii\grid\BooleanColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\qaTask\src\entities\qaTaskStatusReason\search\QaTaskStatusReasonCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Qa Task Status Reasons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qa-task-status-reason-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Qa Task Status Reason', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'tsr_id',
            [
                'class' => QaObjectTypeColumn::class,
                'attribute' => 'tsr_object_type_id',
            ],
            'tsr_status_id:qaTaskStatus',
            'tsr_key',
            'tsr_name',
            [
                'class' => BooleanColumn::class,
                'attribute' => 'tsr_comment_required',
            ],
            [
                'class' => BooleanColumn::class,
                'attribute' => 'tsr_enabled',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'tsr_created_user_id',
                'relation' => 'createdUser'
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'tsr_updated_user_id',
                'relation' => 'updatedUser',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'tsr_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'tsr_updated_dt',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
