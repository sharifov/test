<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\shiftSchedule\src\entities\shiftCategory\ShiftCategory;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel \modules\shiftSchedule\src\entities\shiftCategory\search\ShiftCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shift Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Shift Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'sc_id',
            'sc_name',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'sc_created_user_id',
                'relation' => 'createdUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'sc_updated_user_id',
                'relation' => 'updatedUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
            ['class' => DateTimeColumn::class, 'attribute' => 'sc_created_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'sc_updated_dt'],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, ShiftCategory $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'sc_id' => $model->sc_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
