<?php

use modules\qaTask\src\grid\columns\QaTaskObjectTypeColumn;
use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
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

    <?php Pjax::begin(['scrollTo' => 0]); ?>
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
                'class' => UserSelect2Column::class,
                'attribute' => 'tc_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'tc_updated_user_id',
                'relation' => 'updatedUser',
                'placeholder' => 'Select User',
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
