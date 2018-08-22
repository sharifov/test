<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Lead */
/* @var $searchModel common\models\search\QuoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = 'Lead ID: ' . $model->id . ', UID: '.$model->uid;
$this->params['breadcrumbs'][] = ['label' => 'Leads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
                            return $model->client ? '<i class="fa fa-user"></i> ' . Html::encode($model->client->first_name.' '.$model->client->last_name) : '-';
                        },
                        'options' => ['style' => 'width:160px'],
                        //'filter' => \common\models\Employee::getList()
                    ],

                    [
                        'attribute' => 'client.phone',
                        'header' => 'Client Phones',
                        'format' => 'raw',
                        'value' => function(\common\models\Lead $model) {
                            $str = $model->client && $model->client->clientPhones ? '<i class="fa fa-phone"></i> '.implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')).'' : '';
                            return $str ?? '-';
                        },
                        'options' => ['style' => 'width:180px'],
                    ],


                    [
                        'attribute' => 'client.email',
                        'header' => 'Client Emails',
                        'format' => 'raw',
                        'value' => function(\common\models\Lead $model) {
                            $str = $model->client && $model->client->clientEmails ? '<i class="fa fa-envelope"></i> '.implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')).'' : '';
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
                            return '<h4><span class="label '.$model->getLabelClass().'">'.\common\models\Lead::getStatus($model->status).'</span></h4>';
                        },
                        'format' => 'raw',

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
                    'created',
                    'updated',
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
                'reservation_dump:ntext',
                //'status',
                [
                    'attribute' => 'status',
                    'value' => function(\common\models\Quote $model) {
                        return $model->getStatusName();
                    },
                    'filter' => \common\models\Quote::STATUS_LIST
                ],
                'check_payment:boolean',
                'fare_type',
                'created',
                'updated',

                //['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
        <?php \yii\widgets\Pjax::end(); ?>
        </div>
    </div>


</div>



<?php
yii\bootstrap\Modal::begin([
    'headerOptions' => ['id' => 'modalHeader'],
    'id' => 'modal',
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
//echo "<div id='modalContent'>".\yii\helpers\VarDumper::dumpAsString($model)."</div>";
yii\bootstrap\Modal::end();



$jsCode = <<<JS

    $(document).on('click', '#btn_show_modal', function(){
        
        $('#modalHeader').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
        $('#modal').modal('show');
        
        //.find('#modalContent').html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');
        //$('#modal').modal('show');
        
        //alert($(this).attr('title'));
        /*$('#modalHeader').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
        $.get($(this).attr('href'), function(data) {
          $('#modal').find('#modalContent').html(data);
        });*/
       return false;
    });


JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);