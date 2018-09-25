<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\SqlDataProvider */

$this->title = 'Agents & Leads (Today: '.date("d-M-Y").' )';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="lead-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-12">
        <?//php Pjax::begin(); ?>
        <?php

        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false


            'columns' =>
                [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'label' => 'Agent Id',
                        'attribute' => 'id',

                        'filter' => false,
                    ],
                    [
                        'label' => 'Agent',
                        'attribute' => 'username',
                        'value' => function($data) {
                            return '<b>'.Html::encode($data['username']).'</b>';
                        },
                        'format' => 'html',
                        'filter' => false,
                    ],

                    [
                        'label' => 'Sold',
                        'attribute' => 'st_sold',
                        'value' => function($data) {
                            return $data['st_sold'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],

                    [
                        'label' => 'On Hold',
                        'attribute' => 'st_on_hold',
                        'value' => function($data) {
                            return $data['st_on_hold'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Processing',
                        'attribute' => 'st_processing',
                        'value' => function($data) {
                            return $data['st_processing'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Follow Up',
                        'attribute' => 'st_follow_up',
                        'value' => function($data) {
                            return $data['st_follow_up'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Reject',
                        'attribute' => 'st_reject',
                        'value' => function($data) {
                            return $data['st_reject'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Booked',
                        'attribute' => 'st_booked',
                        'value' => function($data) {
                            return $data['st_booked'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Snooze',
                        'attribute' => 'st_snooze',
                        'value' => function($data) {
                            return $data['st_snooze'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Pending',
                        'attribute' => 'st_pending',
                        'value' => function($data) {
                            return $data['st_pending'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Trash',
                        'attribute' => 'st_trash',
                        'value' => function($data) {
                            return $data['st_trash'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],

                ],
            'toolbar' =>  [
                /*['content'=>
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['leads/duplicate'], ['data-pjax'=>1, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                ],*/
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
                'heading' => '<h3 class="panel-title"><i class="fa fa-copy"></i> Agents</h3>',
            ],

        ]); ?>

        <?//php Pjax::end(); ?>
        </div>
    </div>
</div>
