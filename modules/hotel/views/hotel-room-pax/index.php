<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\hotel\models\search\HotelRoomPaxSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Hotel Room Paxes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hotel-room-pax-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Hotel Room Pax', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'hrp_id',
            'hrp_hotel_room_id',
            'hrp_type_id',
            'hrp_age',
            'hrp_first_name',
            'hrp_last_name',
            'hrp_dob',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
