<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php \yii\widgets\Pjax::begin(); ?>

    <div class="row">
        <div class="col-md-12">

            <p>
                <?=Html::a('Clear all records',['log/clear'],['class' => 'btn btn-danger', 'data' => ['confirm' => 'Delete all records from logs?']]) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    //'id',
                    [
                        'attribute' => 'id',
                        'contentOptions'=>['style'=>'width: 70px;text-align:center;']
                    ],
                    //'level',

                    [
                        'attribute'=>'level',
                        'value'=>function ($model) {
                            return \yii\log\Logger::getLevelName($model->level).' ('.$model->level.')';
                        },
                        'filter'=>[
                            \yii\log\Logger::LEVEL_ERROR            => 'error',
                            \yii\log\Logger::LEVEL_WARNING          => 'warning',
                            \yii\log\Logger::LEVEL_INFO             => 'info',
                            \yii\log\Logger::LEVEL_TRACE            => 'trace',
                            \yii\log\Logger::LEVEL_PROFILE_BEGIN    => 'profile begin',
                            \yii\log\Logger::LEVEL_PROFILE_END      => 'profile end'
                        ],
                        'contentOptions'=>['style'=>'width: 120px;text-align:center;']
                    ],

                    //'category',
                    [
                        'attribute' => 'category',
                        'filter' => \backend\models\Log::getCategoryFilter(),
                        'contentOptions'=>['style'=>'width: 200px;text-align:center;']
                    ],
                    [
                        'attribute' => 'message',
                        'format' => 'raw',
                        'value' => function (\backend\models\Log $model) {
                            $str = '<pre><small>'.\yii\helpers\StringHelper::truncate($model->message, 400, '...', null, true).'</small></pre> 
                            <a href="'.\yii\helpers\Url::to(['log/view', 'id' => $model->id]).'" title="Log '.$model->id.'" class="btn btn-sm btn-success showModalButton" data-pjax="0">show log</a>';
                            return ($str);
                        },
                        //'contentOptions'=>['style'=>'width: 100px;text-align:left;']
                    ],
                    /*[
                        'attribute' => 'log_time',
                        'format' => 'html',
                        'value' => function ($model) {
                            return '<small>'.date('Y-m-d H:i:s', $model->log_time).'</small>';
                        },

                    ],*/

                    [
                        'attribute' => 'log_time',
                        'value' => 'log_time',
                        'format' => 'datetime',
                        'filter' => DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'log_time',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'dd-M-yyyy'
                            ]
                        ]),
                        'contentOptions'=>['style'=>'width: 180px;text-align:center;']
                    ],

                    [
                        'attribute' => 'prefix',
                        'format' => 'html',
                        'value' => function ($model) {
                            return '<small>'.($model->prefix).'</small>';
                        },
                        'contentOptions'=>['style'=>'width: 100px;text-align:left;']
                    ],

                    //'log_time:datetime',
                    //'prefix:ntext',
        //             'message:ntext',

                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {delete}'],
                    //['class' => 'yii\grid\ActionColumn', 'template' => '{delete}'],
                ],
            ]); ?>

        </div>
    </div>

    <?php \yii\widgets\Pjax::end(); ?>
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
        'headerOptions' => ['id' => 'modalHeader'],
        'id' => 'modal',
        'size' => 'modal-lg',
        'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
    ]);
    echo "<div id='modalContent'></div>";
    yii\bootstrap\Modal::end();



$jsCode = <<<JS

    $(document).on('click', '.showModalButton', function(){
        
        $('#modal').modal('show').find('#modalContent').html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');
        //$('#modal').modal('show');
        
        //alert($(this).attr('title'));
        $('#modalHeader').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
        $.get($(this).attr('href'), function(data) {
          $('#modal').find('#modalContent').html(data);
        });
       return false;
    });


JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);