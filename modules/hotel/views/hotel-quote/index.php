<?php

use modules\hotel\models\HotelQuote;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\VarDumper;
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
            'hq_id',
            'hq_hotel_id',
            'hq_hash_key',
            'hq_product_quote_id',
            'hq_destination_name',
            'hq_hotel_name',
            'hq_hotel_list_id',
            [
                'attribute' => 'hq_origin_search_data',
                'value' => static function (HotelQuote $hotelQuote) {
                    if (!$hotelQuote->hq_origin_search_data) {
                        return Yii::$app->formatter->nullDisplay;
                    }

                    $out = '<button class="btn btn-secondary" type="button" data-toggle="collapse" data-target="#item_' . $hotelQuote->hq_id . '" aria-expanded="false" aria-controls="item_' . $hotelQuote->hq_id . '">
                                <i class="fas fa-eye"></i>  ' . $hotelQuote->getAttributeLabel('hq_origin_search_data') . '
                            </button>';
                    $out .= '<div class="collapse" id="item_' . $hotelQuote->hq_id . '">';
                    $out .= '<small>' . VarDumper::dumpAsString($hotelQuote->hq_origin_search_data, 20, true) . '</small>';
                    $out .= '</div>';
                    return $out;
                },
                'format' => 'raw',
                'options' => [
                    'style' => 'width:400px'
                ],
            ],
            'hq_request_hash',

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
