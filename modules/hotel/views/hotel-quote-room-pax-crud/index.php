<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel modules\hotel\models\search\HotelQuoteRoomPaxSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Hotel Quote Room Paxes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hotel-quote-room-pax-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Hotel Quote Room Pax', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'hqrp_id',
            'hqrp_hotel_room_id',
            'hqrp_type_id',
            'hqrp_age',
            'hqrp_first_name',
            //'hqrp_last_name',
            //'hqrp_dob',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
