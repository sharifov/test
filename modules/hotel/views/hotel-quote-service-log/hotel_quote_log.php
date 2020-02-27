<?php

use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLog;
use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLogStatus;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel modules\hotel\src\entities\hotelQuoteServiceLog\search\HotelQuoteServiceLogCrudSearch */
/* @var $filterModel modules\hotel\src\entities\hotelQuoteServiceLog\search\HotelQuoteServiceLogCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="hotel-quote-service-log-index">

    <?php
        $gridColumns = [
            [
                'attribute' => 'hqsl_id',
            ],
            [
                'attribute' => 'hqsl_hotel_quote_id',
            ],
            [
                'attribute' => 'hqsl_action_type_id',
                'value' => static function (HotelQuoteServiceLog $model) {
                    return HotelQuoteServiceLogStatus::ACTION_TYPE_LIST[$model->hqsl_action_type_id];
                },
                'filter' => HotelQuoteServiceLogStatus::ACTION_TYPE_LIST,
            ],
            [
                'attribute' => 'hqsl_status_id',
                'value' => static function (HotelQuoteServiceLog $model) {
                    return HotelQuoteServiceLogStatus::STATUS_LIST[$model->hqsl_status_id];
                },
                'filter' => HotelQuoteServiceLogStatus::STATUS_LIST,
            ],
            [
                'attribute' => 'hqsl_message',
                'value' => static function (HotelQuoteServiceLog $model) {
                    $message = $model->hqsl_message;

                    if (strlen($message) < 600) {
                        return '<pre><small>' . $message . '</small></pre>';
                    } else {
                        $out = '<button class="btn btn-secondary" type="button" data-toggle="collapse" data-target="#item_'. $model->hqsl_id .'" aria-expanded="false" aria-controls="item_'. $model->hqsl_id .'">
                                    <i class="fas fa-eye"></i>  Api Response
                                </button>';
                        $out .= '<div class="collapse" id="item_'. $model->hqsl_id .'">';
                        $out .= '<pre><small>' . $message . '</small></pre>';
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
                'attribute' => 'hqsl_created_user_id',
                'value' => static function (HotelQuoteServiceLog $model) {
                    if ($model->hqsl_created_user_id) {
                        return Html::tag('i', '', ['class' => 'fa fa-user']).' '. Html::encode($model->createdUser->username);
                    } else {
                        return '';
                    }
                },
                'format' => 'raw',
                'filter' => \common\models\Employee::getList()
            ],
            [
                'attribute' => 'hqsl_created_dt',
                'value' => static function (HotelQuoteServiceLog $model) {
                    return $model->hqsl_created_dt ?
                        '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->hqsl_created_dt)) : $model->hqsl_created_dt;
                },
                'format' => 'raw',
                'filter' => $filterModel ? DatePicker::widget([
                    'model' => $filterModel,
                    'attribute' => 'hqsl_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off'
                    ],
                ]) : null,
            ],
        ];
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $filterModel,
        'columns' => $gridColumns,
    ]); ?>

</div>
