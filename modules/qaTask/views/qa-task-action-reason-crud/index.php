<?php

use modules\qaTask\src\entities\qaTaskActionReason\search\QaTaskActionReasonCrudSearch;
use modules\qaTask\src\grid\columns\QaTaskObjectTypeColumn;
use modules\qaTask\src\grid\columns\QaTaskActionColumn;
use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel QaTaskActionReasonCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Qa Task Action Reasons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qa-task-action-reason-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Qa Task Action Reason', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'tar_id',
            [
                'class' => QaTaskObjectTypeColumn::class,
                'attribute' => 'tar_object_type_id',
            ],
            [
                'class' => QaTaskActionColumn::class,
                'attribute' => 'tar_action_id',
            ],
            'tar_key',
            'tar_name',
            [
                'class' => BooleanColumn::class,
                'attribute' => 'tar_comment_required',
            ],
            [
                'class' => BooleanColumn::class,
                'attribute' => 'tar_enabled',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'tar_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'tar_updated_user_id',
                'relation' => 'updatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'tar_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'tar_updated_dt',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
