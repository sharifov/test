<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Clients';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Client', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'first_name',
            'middle_name',
            'last_name',

            [
                'header' => 'Phones',
                'attribute' => 'client_phone',
                'value' => function(\common\models\Client $model) {

                    $phones = $model->clientPhones;
                    $data = [];
                    if($phones) {
                        foreach ($phones as $k => $phone) {
                            $data[] = '<i class="fa fa-phone"></i> <code>'.Html::encode($phone->phone).'</code>'; //<code>'.Html::a($phone->phone, ['client-phone/view', 'id' => $phone->id], ['target' => '_blank', 'data-pjax' => 0]).'</code>';
                        }
                    }

                    $str = implode('<br>', $data);
                    return ''.$str.'';
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-left'],
            ],

            [
                'header' => 'Emails',
                'attribute' => 'client_email',
                'value' => function(\common\models\Client $model) {

                    $emails = $model->clientEmails;
                    $data = [];
                    if($emails) {
                        foreach ($emails as $k => $email) {
                            $data[] = '<i class="fa fa-envelope"></i> <code>'.Html::encode($email->email).'</code>';
                        }
                    }

                    $str = implode('<br>', $data);
                    return ''.$str.'';
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-left'],
            ],

            //'created',
            //'updated',

            [
                'header' => 'Leads',
                'value' => function(\common\models\Client $model) {

                    $leads = $model->leads;
                    $data = [];
                    if($leads) {
                        foreach ($leads as $lead) {
                            $data[] = '<i class="fa fa-link"></i> '. Html::a('lead: '.$lead->id, ['/leads/view', 'id' => $lead->id], ['title' => 'Lead: '. $lead->id, 'class'=>"show-modal", "data-id"=>$lead->id, 'target' => '_blank', 'data-pjax' => 0]).' (IP: '.$lead->request_ip.')';
                        }
                    }

                    $str = '';
                    if($data) {
                        $str = ''.implode('<br>', $data).'';
                    }

                    return $str;
                },
                'format' => 'raw',
                //'options' => ['style' => 'width:100px']
            ],

            [
                'attribute' => 'created',
                'value' => function(\common\models\Client $model) {
                return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created));
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'updated',
                'value' => function(\common\models\Client $model) {
                return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated));
                },
                'format' => 'html',
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
yii\bootstrap\Modal::begin([
    'headerOptions' => ['id' => 'modalClientHeader'],
    //'bodyOptions' => ['id' => 'modalClientContent'],
    'id' => 'modalClient',
    'size' => 'modal-lg',
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);
echo "<div id='modalClientContent'></div>";
yii\bootstrap\Modal::end();



$jsCode = <<<JS

    $(document).on('click', '.show-modal', function(){
        //e.preventDefault();
        $('#modalClient').modal('show').find('#modalClientContent').html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');

        $('#modalClientHeader').html('<h4>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h4>');
        $.get($(this).attr('href'), function(data) {
          $('#modalClient').find('#modalClientContent').html(data);
        });
       return false;
    });


JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);