<?php

use modules\qaTask\src\grid\columns\QaTaskStatusColumn;
use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\qaTask\src\entities\qaTaskStatus\search\QaTaskStatusCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Qa Task Statuses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qa-task-status-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Qa Task Status', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => QaTaskStatusColumn::class,
                'attribute' => 'ts_id'
            ],
            'ts_name',
            'ts_description',
            [
                'class' => BooleanColumn::class,
                'attribute' => 'ts_enabled',
            ],
            'ts_css_class',

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ts_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ts_updated_user_id',
                'relation' => 'updatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ts_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ts_updated_dt',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
