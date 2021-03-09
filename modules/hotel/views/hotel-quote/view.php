<?php

use modules\hotel\models\HotelQuote;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelQuote */

$this->title = $model->hq_id;
$this->params['breadcrumbs'][] = ['label' => 'Hotel Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="hotel-quote-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->hq_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->hq_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="row">
        <div class="col-lg-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'hq_id',
                    'hq_hotel_id',
                    'hq_hash_key',
                    'hq_product_quote_id',
                    'hq_destination_name',
                    'hq_hotel_name',
                    'hq_hotel_list_id',
                    'hq_request_hash',
                    'hq_check_in_date',
                    'hq_check_out_date',
                    'hq_booking_id',
                ],
            ]) ?>
            <br clear="all" />
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'hq_json_booking',
                        'value' => function (HotelQuote $hotelQuote) {
                            if (!$hotelQuote->hq_json_booking) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return VarDumper::dumpAsString($hotelQuote->hq_json_booking, 20, true);
                        },
                        'format' => 'raw',
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-lg-6">
             <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'hq_origin_search_data',
                        'value' => function (HotelQuote $hotelQuote) {
                            if (!$hotelQuote->hq_origin_search_data) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return VarDumper::dumpAsString($hotelQuote->hq_origin_search_data, 20, true);
                        },
                        'format' => 'raw',
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>
