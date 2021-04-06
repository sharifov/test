<?php

use modules\hotel\models\HotelQuoteRoom;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelQuoteRoom */

$this->title = $model->hqr_id;
$this->params['breadcrumbs'][] = ['label' => 'Hotel Quote Rooms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="hotel-quote-room-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->hqr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->hqr_id], [
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
            'hqr_id',
            'hqr_hotel_quote_id',
            'hqr_room_name',
            'hqr_key',
            'hqr_code',
            'hqr_class',
            'hqr_amount',
            'hqr_currency',
            'hqr_payment_type',
            'hqr_board_code',
            'hqr_board_name',
            'hqr_rooms',
            'hqr_adults',
            'hqr_children',
            [
                'attribute' => 'hqr_cancellation_policies',
                'value' => static function (HotelQuoteRoom $model) {
                    if ($model->hqr_cancellation_policies) {
                        return VarDumper::dumpAsString($model->hqr_cancellation_policies, 10, true);
                    }
                    return Yii::$app->formatter->nullDisplay;
                },
                'format' => 'raw'
            ]
        ],
    ]) ?>

</div>
