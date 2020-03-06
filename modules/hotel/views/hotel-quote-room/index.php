<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\hotel\models\search\HotelQuoteRoomSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Hotel Quote Rooms';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hotel-quote-room-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Hotel Quote Room', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'hqr_id',
            'hqr_hotel_quote_id',
            'hqr_room_name',
            'hqr_key',
            'hqr_code',
            'hqr_class',
            'hqr_amount',
            'hqr_currency',
            'hqr_cancel_amount',
            'hqr_cancel_from_dt',
            'hqr_payment_type',
            'hqr_board_code',
            'hqr_board_name',
            'hqr_rooms',
            'hqr_adults',
            'hqr_children',
            'hqr_service_fee_percent:percentInteger',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
