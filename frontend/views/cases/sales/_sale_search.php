<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $caseModel \sales\entities\cases\Cases */
/* @var $isAdmin boolean */
/* @var $searchModel common\models\search\SaleSearch */
/* @var $dataProvider yii\data\ArrayDataProvider */
?>

<div class="x_panel" id="search-sale-panel" style="display: none;">
    <div class="x_title">
        <h2><i class="fa fa-search"></i> Sale Search</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <?/*<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>
            <li><a class="close-link"><i class="fa fa-close"></i></a>
            </li>*/?>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block;">
        <div class="sale-search">

            <h1 title="<?=Yii::$app->params['backOffice']['serverUrl']?>"></h1>

            <?php Pjax::begin(['id' => 'pjax-sale-search-list', 'timeout' => 15000, 'enablePushState' => true]); ?>


            <?php
            //if(Yii::$app->request->isAjax) {
            echo \frontend\themes\gentelella_v2\widgets\FlashAlert::widget();
            //}

            echo $this->render('_sale_search_form', [
                'model' => $searchModel,
                'caseModel' => $caseModel
            ]);



            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => null, //$searchModel,
                'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],

                'rowOptions' => function ($model) {
                    if ($model['saleId'] == '86027') {
                        return ['class' => 'success'];
                    }
                },

                'columns' => [
                    // ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'sale_id',
                        'value' => function ($model) {
                            return $model['saleId'] ?? '-';
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
                        'label' => 'Confirmation Number',
                        'value' => function ($model) {
                            return $model['confirmationNumber'] ?: '-';
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
                        'label' => 'Pax',
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
                        'template' => '{view} {add}',
                        'controller' => 'case',
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="fa fa-search"></span> View', ['sale/view', 'h' => base64_encode($model['confirmationNumber'] . '|' . $model['saleId'])],
                                    ['title' => 'View', 'class' => 'btn btn-xs btn-info showModalCaseInfo', 'data-pjax' => 0]);
                            },
                            'add' => function ($url, $model, $key) use ($caseModel) {
                                return Html::a('<span class="fa fa-plus"></span> Add', ['cases/add-sale'],
                                    ['title' => 'View', 'class' => 'btn btn-xs btn-success addSale', 'data-pjax' => 0, 'data-gid' => $caseModel->cs_gid, 'data-h' => base64_encode($model['confirmationNumber'] . '|' . $model['saleId'])]);
                            },
                        ]
                    ]
                ],

            ]);



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

    </div>
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
    .grid-view pre {
        max-width: 1000px;
    }
</style>


<?php
yii\bootstrap\Modal::begin([
    'headerOptions' => ['id' => 'modalHeader'],
    'id' => 'modalCaseInfo',
    'size' => 'modal-lg',
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);
echo "<div id='modalCaseInfoContent'></div>";
yii\bootstrap\Modal::end();

$jsCode = <<<JS
    $(document).on('click', '.showModalCaseInfo', function(){
        
        $('#modalCaseInfo').modal('show').find('#modalCaseInfoContent').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        //$('#modal').modal('show');
        
        //alert($(this).attr('title'));
        $('#modalHeader').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
        $.get($(this).attr('href'), function(data) {
            $('#modalCaseInfo').find('#modalCaseInfoContent').html(data);
        });
       return false;
    });

    $(document).on('click', '.addSale', function()
    {
        var btn = $(this);
        var h = btn.data('h');
        var gid = btn.data('gid');
        
        btn.addClass('disabled');
        btn.find('span').removeClass('fa-plus').addClass('fa-spinner fa-spin');
        
        $.ajax({
            url: btn.attr('href'),
            type: 'post',
            data: {gid: gid, h: h},
            success: function (data) {
                if (data.error != '') {
                    alert('Error: ' + data.error);
                    btn.removeClass('disabled');
                    btn.find('span').removeClass('fa-spinner fa-spin').addClass('fa-plus');
                    new PNotify({
                        title: "Error add Sale",
                        type: "error",
                        text: 'Error add sale in case',
                        hide: true
                    });
                } else {
                    btn.parent().parent().addClass('success');
                    btn.find('span').removeClass('fa-spinner fa-spin').addClass('fa-check');
                    $.pjax.reload({container: '#pjax-sale-list', push: false, replace: false, timeout: 10000, async: false});
                    new PNotify({
                        title: "Sale successfully added",
                        type: "success",
                        text: 'Sale Id: ' + data.data.sale_id +' successfully added',
                        hide: true
                    });
                }
                
            },
            error: function (error) {
                alert('Server Error');
                console.error('Error: ' + error);
                btn.removeClass('disabled');
                btn.find('span').removeClass('fa-spinner fa-spin').addClass('fa-plus');                
            }
        });
        
       return false;
    });

JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);

