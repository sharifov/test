<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\shiftSchedule\entity\shift\search\SearchShift */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shifts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Shift', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-shift']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'sh_id',
            'sh_name',
            ['class' => \common\components\grid\BooleanColumn::class, 'attribute' => 'sh_enabled'],
            'sh_color',
            'sh_sort_order',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'sh_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'sh_updated_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'sh_created_user_id',
                'relation' => 'createdUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'sh_updated_user_id',
                'relation' => 'updatedUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
