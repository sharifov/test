<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelQuoteRoomPax */

$this->title = $model->hqrp_id;
$this->params['breadcrumbs'][] = ['label' => 'Hotel Quote Room Paxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="hotel-quote-room-pax-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->hqrp_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->hqrp_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'hqrp_id',
            'hqrp_hotel_room_id',
            'hqrp_type_id',
            'hqrp_age',
            'hqrp_first_name',
            'hqrp_last_name',
            'hqrp_dob',
        ],
    ]) ?>

</div>
