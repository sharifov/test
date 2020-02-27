<?php

use modules\qaTask\src\grid\columns\QaTaskObjectTypeColumn;
use sales\yii\grid\BooleanColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\qaTask\src\entities\qaTaskCategory\search\QaTaskCategoryCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Qa Task Categories';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="qa-task-category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Qa Task Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'tc_id',
            'tc_key',
            [
                'class' => QaTaskObjectTypeColumn::class,
                'attribute' => 'tc_object_type_id',
            ],
            'tc_name',
            'tc_description',
            [
                'class' => BooleanColumn::class,
                'attribute' => 'tc_enabled',
            ],
            [
                'class' => BooleanColumn::class,
                'attribute' => 'tc_default',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'tc_created_user_id',
                'relation' => 'createdUser',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'tc_updated_user_id',
                'relation' => 'updatedUser',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'tc_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'tc_updated_dt',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
