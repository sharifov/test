<?php
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SaleSearch */
/* @var $dataProvider yii\data\ArrayDataProvider */

$this->title = 'Search Sale';
$this->params['breadcrumbs'][] = $this->title;


?>
<style>
    .dropdown-menu {
        z-index: 1010;
    }
</style>
<div class="sale-search">

    <h1 title="<?=Yii::$app->params['backOffice']['serverUrl']?>"><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => 'sale-pjax-list', 'timeout' => 15000, 'enablePushState' => true]); ?>


    <?php
    //if(Yii::$app->request->isAjax) {
    echo \frontend\themes\gentelella_v2\widgets\FlashAlert::widget();
    //}

    echo $this->render('_search', [
        'model' => $searchModel
    ]);

    //\yii\helpers\VarDumper::dump($dataProvider->allModels, 10, true);


    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null, //$searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'sale_id',
                'value' => function ($model) {
                    return $model['saleId'];
                },
                'format' => 'raw',
                'options' => [
                    'style' => 'width:80px'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'filter' => false
            ],
            [
                'label' => 'Project',
                'value' => function ($model) {
                    return $model['project'] ? '<span class="badge badge-info">'.Html::encode($model['project']).'</span>': '-';
                },
                'format' => 'raw'
            ],

            /*[
                'label' => 'Air Routing Id',
                'value' => function ($model) {
                    return $model['airRoutingId'] ?: '-';
                },
            ],*/
            [
                'label' => 'Confirmation Number (Booking ID)',
                'value' => function ($model) {
                    return $model['confirmationNumber'] ?: '-';
                },
            ],
            [
                'label' => 'Airline Confirmation Number',
                'value' => function ($model) {
                    return reset($model['airlineConfirmationNumber']) ?: '---';
                },
            ],

            [
                'label' => 'Status',
                'value' => function ($model) {
                    return $model['saleStatus'] ?: '-';
                },
            ],



            [
                'label' => 'Trips',
                'value' => function ($model) {
                    $tripArr = [];
                    if(isset($model['requestDetail']['trips'])) {
                        foreach ($model['requestDetail']['trips'] as $trip) {
                            $tripArr[] = '<span class="label label-default">'.$trip['from'] . '</span> -> <span class="label label-default">' . $trip['to'] . '</span> ['.$trip['departure'].']';
                        }
                    }
                    return implode('<br>', $tripArr);
                },
                'format' => 'raw'
            ],

            [
                'label' => 'PNR Number',
                'value' => function ($model) {
                    return $model['pnr'] ?: '-';
                },
            ],

            [
                'label' => 'Passengers',
                'value' => function ($model) {
                    return isset($model['requestDetail']['passengersCnt']) ? $model['requestDetail']['passengersCnt']: '-';
                },
            ],

            [
                'label' => 'created',
                'value' => function ($model) {
                    return $model['created'] ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model['created'])) : '-';
                },
                'format' => 'raw'
            ],
            // 'created:date',

            /*[
                'attribute' => 'updated',
                'value' => function(\common\models\Lead $model) {
                    $str = '-';
                    if($model->updated) {
                        $str = '<b>' . Yii::$app->formatter->asRelativeTime(strtotime($model->updated)) . '</b>';
                        $str .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated));
                    }
                    return $str;
                },
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
         */


            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="fa fa-search"></span>', ['view', 'h' => base64_encode($model['confirmationNumber'] . '|' . $model['saleId'])], ['title' => 'View', 'data-pjax' => 0]);
                    },
                ]
            ]
        ],

    ]);
    // }

    ?>
    <?php Pjax::end(); ?>
    <?php

    /*$js = <<<JS

        $(document).on('pjax:start', function() {
            $("#modalUpdate .close").click();
        });

        $(document).on('pjax:end', function() {
             $('[data-toggle="tooltip"]').tooltip();
        });


       $('[data-toggle="tooltip"]').tooltip();


    JS;
    $this->registerJs($js, \yii\web\View::POS_READY);*/
    ?>
</div>