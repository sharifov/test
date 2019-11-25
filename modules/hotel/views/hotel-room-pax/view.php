<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelRoomPax */

$this->title = $model->hrp_id;
$this->params['breadcrumbs'][] = ['label' => 'Hotel Room Paxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="hotel-room-pax-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->hrp_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->hrp_id], [
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
            'hrp_id',
            'hrp_hotel_room_id',
            'hrp_type_id',
            'hrp_age',
            'hrp_first_name',
            'hrp_last_name',
            'hrp_dob',
        ],
    ]) ?>

</div>
