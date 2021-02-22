<?php

use common\components\grid\DateTimeColumn;
use yii\grid\SerialColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\rentCar\src\entity\rentCar\RentCarSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rent Cars';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rent-car-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Rent Car', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-rent-car']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],

            //'prc_id',
            'prc_product_id',
            'prc_pick_up_code',
            'prc_drop_off_code',
            ['class' => DateTimeColumn::class, 'attribute' => 'prc_pick_up_date'],
            ['class' => DateTimeColumn::class, 'attribute' => 'prc_drop_off_date'],
            'prc_request_hash_key',
            'prc_pick_up_time',
            'prc_drop_off_time',
            ['class' => DateTimeColumn::class, 'attribute' => 'prc_created_dt'],
            //'prc_updated_dt',
            //'prc_created_user_id',
            //'prc_updated_user_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
