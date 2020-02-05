<?php

use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLog;
use yii\grid\GridView;
use yii\helpers\VarDumper;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel modules\hotel\src\entities\hotelQuoteServiceLog\search\HotelQuoteServiceLogCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="hotel-quote-service-log-index">

    <?php
        $gridColumns = [
            [
                'attribute' => 'hqsl_id',
                'format' => 'raw'
            ],
            [
                'attribute' => 'hqsl_hotel_quote_id',
                'format' => 'raw'
            ],
            [
                'attribute' => 'hqsl_action_type_id',
                'value' => static function (HotelQuoteServiceLog $model) {
                    return HotelQuoteServiceLog::ACTION_TYPE_LIST[$model->hqsl_action_type_id];
                },
                'format' => 'raw',
                'filter' => HotelQuoteServiceLog::ACTION_TYPE_LIST,
            ],
            [
                'attribute' => 'hqsl_status_id',
                'value' => static function (HotelQuoteServiceLog $model) {
                    return HotelQuoteServiceLog::STATUS_LIST[$model->hqsl_status_id];
                },
                'format' => 'raw',
                'filter' => HotelQuoteServiceLog::STATUS_LIST,
            ],
            [
                'attribute' => 'hqsl_message',
                'value' => static function (HotelQuoteServiceLog $model) {
                    $message = VarDumper::dumpAsString(unserialize($model->hqsl_message), 10);

                    if (strlen($message) < 500) {
                        return '<pre>' . $message . '</pre>';
                    } else {
                        $out = '<button class="btn btn-secondary" type="button" data-toggle="collapse" data-target="#item_'. $model->hqsl_id .'" aria-expanded="false" aria-controls="item_'. $model->hqsl_id .'">
                                    Api Response
                                </button>';
                        $out .= '<div class="collapse" id="item_'. $model->hqsl_id .'">';
                        $out .= '<pre>' . $message . '</pre>';
                        $out .= '</div>';

                        return $out;
                    }
                },
                'format' => 'raw',
                'options' => [
                    'style' => 'width:800px'
                ],
            ],
            [
                'attribute' => 'hqsl_created_dt',
                'value' => static function (HotelQuoteServiceLog $model) {
                    return $model->hqsl_created_dt ?
                        '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->hqsl_created_dt)) : $model->hqsl_created_dt;
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'hqsl_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off'
                    ],
                ]),
            ],
        ];
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
    ]); ?>

</div>
