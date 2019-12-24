<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\hotel\models\search\HotelQuoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Hotel Quotes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hotel-quote-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Hotel Quote', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'hq_id',
            'hq_hotel_id',
            'hq_hash_key',
            'hq_product_quote_id',
            'hq_json_response',
            'hq_destination_name',
            'hq_hotel_name',
            'hq_hotel_list_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
