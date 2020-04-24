<?php

use common\models\Client;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;

/* @var yii\web\View $this */
/* @var common\models\search\ClientSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Contacts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>

    <p>
        <?= Html::a('Create Contact', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'uuid',

            /*'first_name',
            'middle_name',
            'last_name',*/
            /* TODO::  */
            [
                'header' => 'Name',
                'attribute' => 'by_name',
                'value' => function(Client $model) {

                    $out = '';
                    $first_name = $model->first_name ? Html::encode($model->first_name) : '<span class="not-set">(not set)</span>';
                    $out .= 'First name: ' . $first_name ;
                    $first_name = $model->first_name ? Html::encode($model->first_name) : '<span class="not-set">(not set)</span>';
                    $out .= 'First name: ' . $first_name ;

                    return $out;
                },
                'format' => 'raw',
            ],

            'company_name',

            'is_company:boolean', /* TODO::  */
            'is_public:boolean',
            'disabled:boolean',
            [
                'header' => 'Phones',
                'attribute' => 'client_phone',
                'value' => function(Client $model) {
                    $phones = $model->clientPhones;
                    $data = [];
                    if($phones) {
                        foreach ($phones as $k => $phone) {
                            $data[] = '<i class="fa fa-phone"></i> <code>' . Html::encode($phone->phone).'</code>';
                        }
                    }
                    return implode('<br>', $data);
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-left'],
            ],
            [
                'header' => 'Emails',
                'attribute' => 'client_email',
                'value' => function(Client $model) {
                    $emails = $model->clientEmails;
                    $data = [];
                    if($emails) {
                        foreach ($emails as $k => $email) {
                            $data[] = '<i class="fa fa-envelope"></i> <code>'.Html::encode($email->email).'</code>';
                        }
                    }
                    return implode('<br>', $data);
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-left'],
            ],
            [
                'header' => 'Leads',
                'value' => function(Client $model) {
                    $leads = $model->leads;
                    $data = [];
                    if($leads) {
                        foreach ($leads as $lead) {
                            $data[] = '<i class="fa fa-link"></i> '. Html::a('lead: '.$lead->id, ['/leads/view', 'id' => $lead->id, 'showInPopUp' => 'modal'], ['title' => 'Lead: '. $lead->id, 'class'=>"show-modal", "data-id"=>$lead->id, 'target' => '_blank', 'data-pjax' => 0]).' (IP: '.$lead->request_ip.')';
                        }
                    }
                    $str = '';
                    if($data) {
                        $str = ''.implode('<br>', $data).'';
                    }
                    return $str;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'created',
                'value' => function(Client $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],
            [
                'attribute' => 'updated',
                'value' => function(Client $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'updated',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>


    <style type="text/css">
        @media screen and (min-width: 768px) {
            .modal-dialog {
                width: 700px; /* New width for default modal */
            }
            .modal-sm {
                width: 350px; /* New width for small modal */
            }
        }
        @media screen and (min-width: 992px) {
            .modal-lg {
                width: 80%; /* New width for large modal */
            }
        }
    </style>

<?php
yii\bootstrap4\Modal::begin([
    'id' => 'modalClient',
    'size' => \yii\bootstrap4\Modal::SIZE_LARGE,
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);
echo "<div id='modalClientContent'></div>";
yii\bootstrap4\Modal::end();



$jsCode = <<<JS

    $(document).on('click', '.show-modal', function(){
        //e.preventDefault();
        $('#modalClient').modal('show').find('#modalClientContent').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');

        $('#modalClient-label').html($(this).attr('title'));
        $.get($(this).attr('href'), function(data) {
          $('#modalClient').find('#modalClientContent').html(data);
        });
       return false;
    });


JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);