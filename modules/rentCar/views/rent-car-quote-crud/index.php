<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\rentCar\src\entity\rentCarQuote\RentCarQuoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rent Car Quotes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rent-car-quote-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Rent Car Quote', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-rent-car-quote']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'rcq_id',
            'rcq_rent_car_id',
            'rcq_product_quote_id',
            'rcq_hash_key',
            'rcq_request_hash_key',
            //'rcq_json_response',
            'rcq_model_name',
            'rcq_category',
            //'rcq_image_url:url',
            'rcq_vendor_name',
            //'rcq_vendor_logo_url:url',
            'rcq_transmission',
            'rcq_seats',
            'rcq_doors',
            //'rcq_options',
            'rcq_days',
            'rcq_price_per_day',
            'rcq_currency',
            //'rcq_advantages',
            'rcq_pick_up_location',
            'rcq_drop_of_location',
            //'rcq_created_dt',
            //'rcq_updated_dt',
            //'rcq_created_user_id',
            //'rcq_updated_user_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
