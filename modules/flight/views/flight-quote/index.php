<?php

use modules\flight\models\FlightQuote;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;
use yii\bootstrap4\Modal;

/* @var $this yii\web\View */
/* @var $searchModel modules\flight\models\search\FlightQuoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Quotes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight Quote', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'fq_id',
            'fq_flight_id',
            'fq_source_id',
            'fq_product_quote_id',
            'fq_hash_key',
            'fq_uid',
            'fq_service_fee_percent',
            'fq_record_locator',
            'fq_gds',
            'fq_gds_pcc',
            'fq_gds_offer_id',
            'fq_type_id',
            'fq_cabin_class',
            'fq_trip_type_id',
            'fq_main_airline',
            'fq_fare_type_id',
            /*[
                'label' => 'Ticket exist',
                'attribute' => 'ticketExist',
                'value' => static function (FlightQuote $model) {
                    return Yii::$app->formatter->asBooleanByLabel(!empty($model->fq_ticket_json));
                },
                'format' => 'raw',
                'filter' => [1 => 'Yes', 0 => 'No'],
            ],*/
            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'fq_created_user_id',
                'relation' => 'fqCreatedUser',
                'placeholder' => 'Select User',
            ],

            'fq_created_expert_id',
            'fq_created_expert_name',
            'fq_reservation_dump:ntext',
            'fq_pricing_info:ntext',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'OSD',
                'headerOptions' => ['title' => 'Origin Search Data'],
                'template' => '{popupview}',
                'buttons' => [
                    'popupview' => function ($url, $model) {
                        $url = Url::to(['flight-quote/show-osd' , 'id' => $model->fq_id]);
                        return Html::a('<span class="fas fa-eye white"></span>', $url, ['class' => 'btn btn-info show-osd', 'id' => 'viewButton', 'title' => 'Original Search Data', 'data-pjax' => 0]);
                    },
                ],
            ],
            //'fq_origin_search_data',
            //'fq_last_ticket_date',
            [
                'attribute' => 'fq_last_ticket_date',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'fq_last_ticket_date',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'clearBtn' => true,
                        'endDate' => date('Y-m-d', time())
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date'
                    ],
                    'containerOptions' => [
                        'class' => (array_key_exists('fq_last_ticket_date', $searchModel->errors)) ? 'has-error' : null,
                    ],
                    'clientEvents' => [
                        'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                    ],
                ]),
            ],
            'fq_request_hash',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
Modal::begin([
    'title' => '<h4>Original Search Data</h4>',
    'id' => 'view',
    'size' => 'modal-lg',
]);

echo "<pre><div id='viewContent'></div></pre>";

Modal::end();
?>

<?php
$jsCode = <<<JS
    
    $(document).on('click', '.show-osd', function(){        
        $('#view').modal('show')
            .find('#viewContent')
            .load($(this).attr('href'));
        return false;
    });

JS;
$this->registerJs($jsCode, \yii\web\View::POS_READY);
?>