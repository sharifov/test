<?php

use src\helpers\email\MaskEmailHelper;
use src\helpers\phone\MaskPhoneHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProviderEmail yii\data\SqlDataProvider */
/* @var $dataProviderPhone yii\data\SqlDataProvider */
/* @var $dataProviderIp yii\data\SqlDataProvider */

$this->title = 'Find Duplicate Leads';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="lead-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-4">
        <?php //php Pjax::begin(); ?>
        <?php

        echo GridView::widget([
            'dataProvider' => $dataProviderPhone,
            'filterModel' => $searchModel,
            //'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false


            'columns' =>
                [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'label' => 'Leads',
                        'attribute' => 'cnt',
                        'filter' => false,
                    ],
                    [
                        'attribute' => 'client_phone',
                        'value' => function ($model) {
                            return Html::a(MaskPhoneHelper::masking($model['client_phone']), ['leads/index', 'LeadSearch[client_phone]' => MaskPhoneHelper::masking($model['client_phone'])], ['data-pjax' => 0, 'target' => '_blank']);
                        },
                        'format' => 'raw',
                    ],
                ],
            'toolbar' =>  [
                ['content' =>
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['leads/duplicate'], ['data-pjax' => 1, 'class' => 'btn btn-default', 'title' => 'Reset Grid'])
                ],
                //'{export}',
                //$fullExportMenu,
                //'{toggleData}'
            ],
            'pjax' => true,
            //'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
            //'bordered' => true,
            'striped' => false,
            'condensed' => false,
            'responsive' => true,
            'hover' => true,
            'floatHeader' => false,
            //'floatHeaderOptions' => ['scrollingTop' => 20],
            /*'showPageSummary' => true,*/
            'panel' => [
                'type' => GridView::TYPE_PRIMARY,
                'heading' => '<h3 class="panel-title"><i class="fa fa-copy"></i> Duplicate Leads by Phone</h3>',
            ],

        ]); ?>

        <?php //php Pjax::end(); ?>
        </div>


        <div class="col-md-4">
            <?php //php Pjax::begin(); ?>
            <?php

            echo GridView::widget([
                'dataProvider' => $dataProviderEmail,
                'filterModel' => $searchModel,
                //'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false


                'columns' =>
                    [
                        ['class' => 'yii\grid\SerialColumn'],

                        [
                            //'header' => 'Leads',
                            'label' => 'Leads',
                            'attribute' => 'cnt',
                            'filter' => false,
                        ],

                        [
                            //'header' => 'Email',
                            'attribute' => 'client_email',
                            'value' => function ($model) {
                                return Html::a(MaskEmailHelper::masking($model['client_email']), ['leads/index', 'LeadSearch[client_email]' => MaskEmailHelper::masking($model['client_email'])], ['data-pjax' => 0, 'target' => '_blank']);
                            },
                            'format' => 'raw',

                        ],
                    ],
                'toolbar' =>  [
                    ['content' =>
                        Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['leads/duplicate'], ['data-pjax' => 1, 'class' => 'btn btn-default', 'title' => 'Reset Grid'])
                    ],
                    //'{export}',
                    //$fullExportMenu,
                    //'{toggleData}'
                ],
                'pjax' => true,
                //'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
                //'bordered' => true,
                'striped' => false,
                'condensed' => false,
                'responsive' => true,
                'hover' => true,
                'floatHeader' => false,
                //'floatHeaderOptions' => ['scrollingTop' => 20],
                /*'showPageSummary' => true,*/
                'panel' => [
                    'type' => GridView::TYPE_PRIMARY,
                    'heading' => '<h3 class="panel-title"><i class="fa fa-copy"></i> Duplicate Leads by Email</h3>',
                ],

            ]); ?>

            <?php //php Pjax::end(); ?>
        </div>

        <div class="col-md-4">
            <?php //php Pjax::begin(); ?>
            <?php

            echo GridView::widget([
                'dataProvider' => $dataProviderIp,
                'filterModel' => $searchModel,
                //'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false


                'columns' =>
                    [
                        ['class' => 'yii\grid\SerialColumn'],

                        [
                            'label' => 'Leads',
                            'attribute' => 'cnt',
                            'filter' => false,
                        ],

                        [
                            'attribute' => 'request_ip',
                            'value' => function ($model) {
                                return Html::a($model['request_ip'], ['leads/index', 'LeadSearch[request_ip]' => $model['request_ip']], ['data-pjax' => 0, 'target' => '_blank']);
                            },
                            'format' => 'raw',

                        ],
                    ],
                'toolbar' =>  [
                    ['content' =>
                        Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['leads/duplicate'], ['data-pjax' => 1, 'class' => 'btn btn-default', 'title' => 'Reset Grid'])
                    ],
                    //'{export}',
                    //$fullExportMenu,
                    //'{toggleData}'
                ],
                'pjax' => true,
                //'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
                //'bordered' => true,
                'striped' => false,
                'condensed' => false,
                'responsive' => true,
                'hover' => true,
                'floatHeader' => false,
                //'floatHeaderOptions' => ['scrollingTop' => 20],
                /*'showPageSummary' => true,*/
                'panel' => [
                    'type' => GridView::TYPE_PRIMARY,
                    'heading' => '<h3 class="panel-title"><i class="fa fa-copy"></i> Duplicate Leads by IP</h3>',
                ],

            ]); ?>

            <?php //php Pjax::end(); ?>
        </div>

    </div>
</div>
