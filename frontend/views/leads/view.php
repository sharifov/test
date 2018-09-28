<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Lead */
/* @var $searchModel common\models\search\QuoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

/* @var $searchModelSegments common\models\search\LeadFlightSegmentSearch */
/* @var $dataProviderSegments yii\data\ActiveDataProvider */


$this->title = 'Lead ID: ' . $model->id . ', UID: '.$model->uid;
$this->params['breadcrumbs'][] = ['label' => 'Leads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$isAgent = Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id);

?>
<div class="lead-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?//= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?= Html::a('<i class="fa fa-search"></i> View Lead', '/lead/booked/' . $model->id, ['class' => 'btn btn-primary']) ?>




        <?/*= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <div class="row">

        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'uid',
                    'client_id',
                    [
                        'attribute' => 'client.name',
                        'header' => 'Client name',
                        'format' => 'raw',
                        'value' => function(\common\models\Lead $model) {
                            if($model->client) {
                                $clientName = $model->client->first_name . ' ' . $model->client->last_name;
                                if ($clientName === 'Client Name') {
                                    $clientName = '- - - ';
                                } else {
                                    $clientName = '<i class="fa fa-user"></i> '. Html::encode($clientName);
                                }
                            } else {
                                $clientName = '-';
                            }

                            return $clientName;
                        },
                        'options' => ['style' => 'width:160px'],
                        //'filter' => \common\models\Employee::getList()
                    ],

                    [
                        'attribute' => 'client.phone',
                        'header' => 'Client Phones',
                        'format' => 'raw',
                        'value' => function(\common\models\Lead $model) use ($isAgent) {
                            if($model->client && $model->client->clientPhones) {
                                if ($isAgent && Yii::$app->user->id !== $model->employee_id) {
                                    $str = '- // - // - // -';
                                } else {
                                    $str = '<i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone'));
                                }
                            } else {
                                $str = '-';
                            }

                            return $str ?? '-';
                        },
                        'options' => ['style' => 'width:180px'],
                    ],


                    [
                        'attribute' => 'client.email',
                        'header' => 'Client Emails',
                        'format' => 'raw',
                        'value' => function(\common\models\Lead $model) use ($isAgent) {

                            if($model->client && $model->client->clientEmails) {
                                if ($isAgent && Yii::$app->user->id !== $model->employee_id) {
                                    $str = '- // - // - // -';
                                } else {
                                    $str = '<i class="fa fa-envelope"></i> '.implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email'));
                                }
                            } else {
                                $str = '-';
                            }

                            return $str ?? '-';
                        },
                        'options' => ['style' => 'width:180px'],
                    ],

                    [
                        'attribute' => 'employee_id',
                        'format' => 'raw',
                        'value' => function(\common\models\Lead $model) {
                            return $model->employee ? '<i class="fa fa-user"></i> '.$model->employee->username : '-';
                        },
                    ],

                    //'employee_id',



                    [
                        'attribute' => 'status',
                        'value' => function(\common\models\Lead $model) {
                            return $model->getStatusName(true);
                        },
                        'format' => 'html',

                    ],
                    [
                        'attribute' => 'project_id',
                        'value' => function(\common\models\Lead $model) {
                            return $model->project ? $model->project->name : '-';
                        },

                    ],

                    [
                        'attribute' => 'source_id',
                        'value' => function(\common\models\Lead $model) {
                            return $model->source ? $model->source->name : '-';
                        },
                        'visible' => !$isAgent
                    ],



                ],
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'trip_type',
                        'value' => function(\common\models\Lead $model) {
                            return \common\models\Lead::getFlightType($model->trip_type) ?? '-';
                        },

                    ],

                    [
                        'attribute' => 'cabin',
                        'value' => function(\common\models\Lead $model) {
                            return \common\models\Lead::getCabin($model->cabin) ?? '-';
                        },

                    ],

                    /*'project_id',
                    'source_id',
                    'trip_type',
                    'cabin',*/
                    'adults',
                    'children',
                    'infants',
                    'notes_for_experts:ntext',


                    [
                        'attribute' => 'created',
                        'value' => function(\common\models\Lead $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created));
                        },
                        'format' => 'html',
                    ],

                    [
                        'attribute' => 'updated',
                        'value' => function(\common\models\Lead $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated));
                        },
                        'format' => 'html',
                    ],
                ],
            ]) ?>
        </div>

        <div class="col-md-4">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [


                    //'request_ip',
                    [
                        'attribute' => 'request_ip',
                        'value' => function(\common\models\Lead $model) {
                            return $model->request_ip ? Html::button($model->request_ip, ['class' => 'btn btn-info',  'id' => 'btn_show_modal', 'title' => 'Detail IP info: ' . $model->request_ip]) : '-';
                        },
                        'format' => 'raw'

                    ],
                    //'request_ip_detail:ntext',
                    'offset_gmt',
                    'snooze_for',
                    'rating',
                    'called_expert',
                    'discount_id',
                    'bo_flight_id',
                ],
            ]) ?>

            <? /*if($model->request_ip_detail): ?>
            <pre>
                <?
                    $data = @json_decode($model->request_ip_detail);
                    \yii\helpers\VarDumper::dump($data, 10, true);
                ?>
            </pre>
            <? endif;*/ ?>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <h3>Flight Segments:</h3>
            <?php \yii\widgets\Pjax::begin(); ?>

            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProviderSegments,
                'filterModel' => $searchModelSegments,
                'columns' => [
                    'id',
                    /*[
                        'attribute' => 'lead_id',
                        'format' => 'raw',
                        'value' => function(\common\models\LeadFlightSegment $model) {
                            return '<i class="fa fa-arrow-right"></i> '.Html::a('lead: '.$model->lead_id, ['leads/view', 'id' => $model->lead_id], ['target' => '_blank', 'data-pjax' => 0]);
                        },
                    ],*/
                    'origin',
                    'destination',
                    [
                        'attribute' => 'departure',
                        'value' => function(\common\models\LeadFlightSegment $model) {
                            return '<i class="fa fa-calendar"></i> '.date("Y-m-d", strtotime($model->departure));
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'flexibility',
                        'value' => function(\common\models\LeadFlightSegment $model) {
                            return $model->flexibility;
                        },
                        'filter' => array_combine(range(0, 5), range(0, 5)),
                    ],
                    [
                        'attribute' => 'flexibility_type',
                        'value' => function(\common\models\LeadFlightSegment $model) {
                            return $model->flexibility_type;
                        },
                        'filter' => \common\models\LeadFlightSegment::FLEX_TYPE_LIST
                    ],
                    [
                        'attribute' => 'created',
                        'value' => function(\common\models\LeadFlightSegment $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created));
                        },
                        'format' => 'html',
                    ],

                    [
                        'attribute' => 'updated',
                        'value' => function(\common\models\LeadFlightSegment $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated));
                        },
                        'format' => 'html',
                    ],

                    'origin_label',
                    'destination_label',

                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view}', 'controller' => 'lead-flight-segment'],
                ],
            ]); ?>

            <?php \yii\widgets\Pjax::end(); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3>Quotes:</h3>
        <?php \yii\widgets\Pjax::begin(); ?>
        <p>
            <?//= Html::a('Create Lead', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?= \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],

                'id',
                'uid',
                //'lead_id',
                //'employee_id',
                [
                    'attribute' => 'employee_id',
                    'format' => 'raw',
                    'value' => function(\common\models\Quote $model) {
                        return $model->employee ? '<i class="fa fa-user"></i> '.$model->employee->username : '-';
                    },
                    'filter' => \common\models\Employee::getList()
                ],
                'record_locator',

                //'cabin',
                //'gds',

                [
                    'attribute' => 'gds',
                    'value' => function(\common\models\Quote $model) {
                        return '<i class="fa fa-plane"></i> '.$model->getGdsName2();
                    },
                    'format' => 'raw',
                    'filter' => \common\models\Quote::GDS_LIST
                ],

                'pcc',

                [
                    'attribute' => 'trip_type',
                    'value' => function(\common\models\Quote $model) {
                        return \common\models\Lead::getFlightType($model->trip_type) ?? '-';
                    },
                    'filter' => \common\models\Lead::TRIP_TYPE_LIST
                ],

                [
                    'attribute' => 'cabin',
                    'value' => function(\common\models\Quote $model) {
                        return \common\models\Lead::getCabin($model->cabin) ?? '-';
                    },
                    'filter' => \common\models\Lead::CABIN_LIST
                ],
                //'trip_type',
                'main_airline_code',
                //'reservation_dump:ntext',

                [
                    'attribute' => 'reservation_dump',
                    'value' => function(\common\models\Quote $model) {
                        return '<pre style="font-size: 9px">'. $model->reservation_dump . '</pre>';
                    },
                    'format' => 'html',
                ],

                //'status',
                [
                    'attribute' => 'status',
                    'value' => function(\common\models\Quote $model) {
                        return $model->getStatusName(true);
                    },
                    'format' => 'html',
                    'filter' => \common\models\Quote::STATUS_LIST
                ],
                'check_payment:boolean',
                'fare_type',


                [
                    'header' => 'Prices',
                    'value' => function(\common\models\Quote $model) {
                        return $model->quotePricesCount ? Html::a($model->quotePricesCount, ['quote-price/index', "QuotePriceSearch[quote_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-center'],
                ],

                //'created',
                //'updated',

                [
                    'attribute' => 'created',
                    'value' => function(\common\models\Quote $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created));
                    },
                    'format' => 'html',
                ],

                [
                    'attribute' => 'updated',
                    'value' => function(\common\models\Quote $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated));
                    },
                    'format' => 'html',
                ],

                ['class' => 'yii\grid\ActionColumn', 'template' => '{view}', 'controller' => 'quote'],

                //['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
        <?php \yii\widgets\Pjax::end(); ?>
        </div>
    </div>


</div>



<?php
yii\bootstrap\Modal::begin([
    'headerOptions' => ['id' => 'modal-ip-Header'],
    'id' => 'modal-ip',
    'size' => 'modal-lg',
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);

if($model->request_ip_detail){
    $data = @json_decode($model->request_ip_detail);

    if($data) {
        echo '<pre>';
        \yii\helpers\VarDumper::dump($data, 10, true);
        echo '</pre>';
    }
}
yii\bootstrap\Modal::end();


$jsCode = <<<JS
    $(document).on('click', '#btn_show_modal', function(){
        $('#modal-ip-Header').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
        $('#modal-ip').modal('show');
        return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);