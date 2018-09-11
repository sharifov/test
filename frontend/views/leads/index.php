<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Search Leads';
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
.dropdown-menu {
    z-index: 1010;
}
</style>
<div class="lead-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?//php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>



    <p>
        <?//= Html::a('Create Lead', ['create'], ['class' => 'btn btn-success']) ?>
    </p>



    <?php

    $gridColumnsExport = [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'id',
        ],

        [
            'attribute' => 'status',
            'value' => function(\common\models\Lead $model) {
                return $model->getStatusName(false);
            },
        ],

        [
            'header' => 'Segments',
            'value' => function(\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $segmentData = [];
                if($segments) {
                    foreach ($segments as $sk => $segment) {
                        $segmentData[] = ($sk + 1).'. '.($segment->origin.'->'.$segment->destination).'';
                    }
                }

                $segmentStr = implode("\r\n", $segmentData);
                return ''.$segmentStr.'';
                //return $model->leadFlightSegmentsCount ? Html::a($model->leadFlightSegmentsCount, ['lead-flight-segment/index', "LeadFlightSegmentSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
            },

        ],


        [
            'header' => 'Origin City Code',
            'value' => function(\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $originCode = null;
                $destinationCode = null;

                if($segments) {
                    foreach ($segments as $sk => $segment) {

                        if(!$originCode) {
                            $originCode = $segment->origin;
                        }
                        $destinationCode = $segment->destination;
                    }
                }

                return $originCode;
            },
        ],


        [
            'header' => 'Destination City Code',
            'value' => function(\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $originCode = null;
                $destinationCode = null;

                if($segments) {
                    foreach ($segments as $sk => $segment) {

                        if(!$originCode) {
                            $originCode = $segment->origin;
                        }
                        $destinationCode = $segment->destination;
                    }
                }

                return $destinationCode;
            },
        ],

        [
            'header' => 'Origin City, full name',
            'value' => function(\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $originCode = null;
                $destinationCode = null;

                if($segments) {
                    foreach ($segments as $sk => $segment) {

                        if(!$originCode) {
                            $originCode = $segment->origin;
                        }
                        $destinationCode = $segment->destination;
                    }
                }

                $city = '-';
                if($originCode) {
                    $airport = \common\models\AirportList::find()->where(['ai_iata_code' => $originCode])->one();
                    if($airport && $airport->aiRegionIsoCode) {
                        $city = $airport->aiRegionIsoCode->r_name;
                    }
                }

                return $city;
            },
        ],


        [
            'header' => 'Destination City, full name',
            'value' => function(\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $originCode = null;
                $destinationCode = null;

                if($segments) {
                    foreach ($segments as $sk => $segment) {

                        if(!$originCode) {
                            $originCode = $segment->origin;
                        }
                        $destinationCode = $segment->destination;
                    }
                }

                $city = '-';
                if($destinationCode) {
                    $airport = \common\models\AirportList::find()->where(['ai_iata_code' => $destinationCode])->one();
                    if($airport && $airport->aiRegionIsoCode) {
                        $city = $airport->aiRegionIsoCode->r_name;
                    }
                }

                return $city;
            },
        ],

        [
            'header' => 'Origin Country',
            'value' => function(\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $originCode = null;
                $destinationCode = null;

                if($segments) {
                    foreach ($segments as $sk => $segment) {

                        if(!$originCode) {
                            $originCode = $segment->origin;
                        }
                        $destinationCode = $segment->destination;
                    }
                }

                $country = '-';
                if($originCode) {
                    $airport = \common\models\AirportList::find()->where(['ai_iata_code' => $originCode])->one();
                    if($airport && $airport->aiCountryIsoCode) {
                        $country = $airport->aiCountryIsoCode->c_iso_code;
                    }
                }

                return $country;
            },
        ],


        [
            'header' => 'Destination Country',
            'value' => function(\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $originCode = null;
                $destinationCode = null;

                if($segments) {
                    foreach ($segments as $sk => $segment) {

                        if(!$originCode) {
                            $originCode = $segment->origin;
                        }
                        $destinationCode = $segment->destination;
                    }
                }

                $country = '-';
                if($destinationCode) {
                    $airport = \common\models\AirportList::find()->where(['ai_iata_code' => $destinationCode])->one();
                    if($airport && $airport->aiCountryIsoCode) {
                        $country = $airport->aiCountryIsoCode->c_iso_code;
                    }
                }

                return $country;
            },
        ],



        [
            'header' => 'Profit',
            'value' => function(\common\models\Lead $model) {
                $total = 0;
                $quote = \common\models\Quote::find()->where(['lead_id' => $model->id, 'status' => \common\models\Quote::STATUS_APPLIED])->orderBy(['id' => SORT_DESC])->one();

                if(!$quote) {
                    $quote = \common\models\Quote::find()->where(['lead_id' => $model->id, 'status' => \common\models\Quote::STATUS_SEND])->orderBy(['id' => SORT_DESC])->one();
                }

                if($quote) {
                    $prices = $quote->quotePrices;
                    if($prices) {
                        foreach ($prices as $price) {
                            $total += (float) $price->selling - (float) $price->net;
                        }
                    }

                }
                return $total;
            },
        ],

        [
            'header' => 'Outbound Date',
            'value' => function(\common\models\Lead $model) {
                $segments = $model->leadFlightSegments;
                $datetime = '';
                if(isset($segments[0]) && $segments[0]->departure) {
                    $datetime = date('d-M-Y', strtotime($segments[0]->departure));
                }
                return $datetime;
            },
        ],

        [
            'header' => 'Market info',
            'value' => function(\common\models\Lead $model) {
                return $model->source ? $model->source->name : '-';
            },
        ],


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



        [
            'attribute' => 'adults',
            'value' => function(\common\models\Lead $model) {
                return $model->adults ?: 0;
            },

        ],

        [
            'attribute' => 'children',
            'value' => function(\common\models\Lead $model) {
                return $model->children ?: 0;
            },

        ],

        [
            'attribute' => 'infants',
            'value' => function(\common\models\Lead $model) {
                return $model->infants ?: 0;
            },
        ],


        [
            'header' => 'Created Date',
            'value' => function(\common\models\Lead $model) {
                return Yii::$app->formatter->asDatetime($model->created, 'php:d-M-Y');
            },
        ],

        [
            'header' => 'Created Time',
            'value' => function(\common\models\Lead $model) {
                return Yii::$app->formatter->asDatetime($model->created, 'php:H:i');
            },
        ],



    ];



    /*$fullExportMenu = ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumnsExport,
        'target' => ExportMenu::TARGET_BLANK,
        'fontAwesome' => true,
        'pjaxContainerId' => 'kv-pjax-container',
        'dropdownOptions' => [
            'label' => 'Full',
            'class' => 'btn btn-default',
            'itemsBefore' => [
                '<li class="dropdown-header">Export All Data</li>',
            ],
        ],
    ]);*/

        $gridColumns = [
            //['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'options' => ['style' => 'width:80px'],
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'attribute' => 'uid',
                'options' => ['style' => 'width:100px'],
                'contentOptions' => ['class' => 'text-center'],
            ],

            [   'attribute' => 'client_id',
                'options' => ['style' => 'width:80px'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                //'attribute' => 'client_id',
                'header' => 'Client name',
                'format' => 'raw',
                'value' => function(\common\models\Lead $model) {
                    return $model->client ? '<i class="fa fa-user"></i> ' . Html::encode($model->client->first_name.' '.$model->client->last_name) : '-';
                },
                'options' => ['style' => 'width:160px'],
                //'filter' => \common\models\Employee::getList()
            ],

            [
                'header' => 'Client Emails/Phones',
                'format' => 'raw',
                'value' => function(\common\models\Lead $model) {
                    $str = $model->client && $model->client->clientEmails ? '<i class="fa fa-envelope"></i> '.implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')).'' : '';
                    $str .= $model->client && $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> '.implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')).'' : '';

                    return $str ?? '-';
                },
                'options' => ['style' => 'width:180px'],
            ],

            /*[
                'header' => 'Client Phones',
                'value' => function(\common\models\Lead $model) {
                    return $model->client && $model->client->clientPhones ? implode(', ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) : '-';
                },
            ],*/

            //'employee_id',
            //'status',
            [
                'attribute' => 'status',
                'value' => function(\common\models\Lead $model) {
                    return $model->getStatusName(true);
                },
                'format' => 'html',
                'filter' => \common\models\Lead::STATUS_LIST
            ],
            [
                'attribute' => 'project_id',
                'value' => function(\common\models\Lead $model) {
                    return $model->project ? $model->project->name : '-';
                },
                'filter' => \common\models\Project::getList()
            ],


            //'project_id',
            //'source_id',
            [
                'attribute' => 'source_id',
                'value' => function(\common\models\Lead $model) {
                    return $model->source ? $model->source->name : '-';
                },
                'filter' => \common\models\Source::getList()
            ],

            [
                'attribute' => 'trip_type',
                'value' => function(\common\models\Lead $model) {
                    return \common\models\Lead::getFlightType($model->trip_type) ?? '-';
                },
                'filter' => \common\models\Lead::TRIP_TYPE_LIST
            ],

            [
                'attribute' => 'cabin',
                'value' => function(\common\models\Lead $model) {
                    return \common\models\Lead::getCabin($model->cabin) ?? '-';
                },
                'filter' => \common\models\Lead::CABIN_LIST
            ],

            //'trip_type',
            //'cabin',
            //'adults',

            [
                'attribute' => 'adults',
                'value' => function(\common\models\Lead $model) {
                    return $model->adults ?: 0;
                },
                'filter' => array_combine(range(0, 9), range(0, 9)),
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'attribute' => 'children',
                'value' => function(\common\models\Lead $model) {
                    return $model->children ?: '-';
                },
                'filter' => array_combine(range(0, 9), range(0, 9)),
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'attribute' => 'infants',
                'value' => function(\common\models\Lead $model) {
                    return $model->infants ?: '-';
                },
                'filter' => array_combine(range(0, 9), range(0, 9)),
                'contentOptions' => ['class' => 'text-center'],
            ],


            [
                'header' => 'Quotes',
                'value' => function(\common\models\Lead $model) {
                    return $model->quotesCount ? Html::a($model->quotesCount, ['quote/index', "QuoteSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'header' => 'Segments',
                'value' => function(\common\models\Lead $model) {

                    $segments = $model->leadFlightSegments;
                    $segmentData = [];
                    if($segments) {
                        foreach ($segments as $sk => $segment) {
                            $segmentData[] = ($sk + 1).'. <code>'.Html::a($segment->origin.'->'.$segment->destination, ['lead-flight-segment/view', 'id' => $segment->id], ['target' => '_blank', 'data-pjax' => 0]).'</code>';
                        }
                    }

                    $segmentStr = implode('<br>', $segmentData);
                    return ''.$segmentStr.'';
                    //return $model->leadFlightSegmentsCount ? Html::a($model->leadFlightSegmentsCount, ['lead-flight-segment/index', "LeadFlightSegmentSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
            ],

            //'children',
            //'infants',
            //'notes_for_experts:ntext',

            //'updated',
            //'request_ip',
            //'request_ip_detail:ntext',

            [
                'attribute' => 'employee_id',
                'format' => 'raw',
                'value' => function(\common\models\Lead $model) {
                    return $model->employee ? '<i class="fa fa-user"></i> '.$model->employee->username : '-';
                },
                'filter' => \common\models\Employee::getList()
            ],

            //'rating',
            //'called_expert',
            /*[
                'attribute' => 'discount_id',
                'options' => ['style' => 'width:100px'],
                'contentOptions' => ['class' => 'text-center'],
            ],*/
            //'offset_gmt',
            //'snooze_for',
            //'created',
            [
                'attribute' => 'created',
                'value' => function(\common\models\Lead $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime($model->created, 'php:Y-m-d [H:i]');
                },
                'format' => 'html',
            ],

            /*[
                'attribute' => 'updated',
                'value' => function(\common\models\Lead $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime($model->updated, 'php:Y-m-d [H:i]');
                },
                'format' => 'html',
            ],*/
            //'bo_flight_id',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ];

    //$fullExportMenu =

        Yii::$app->state = Yii::$app::STATE_END;



        $fullExportMenu = ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumnsExport,
            'fontAwesome' => true,
            //'stream' => false, // this will automatically save file to a folder on web server
            //'deleteAfterSave' => false, // this will delete the saved web file after it is streamed to browser,
            //'batchSize' => 10,
            'target' => ExportMenu::TARGET_BLANK,
            'linkPath' => '/assets/',
            'folder' => '@webroot/assets', // this is default save folder on server
        ]);





    //echo Yii::getAlias('@webroot/assets'); exit;


    /*$fullExportMenu = ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'batchSize' => 10,
        'columns' => $gridColumns,
        'target' => ExportMenu::TARGET_BLANK,
        'fontAwesome' => true,
        'asDropdown' => false, // this is important for this case so we just need to get a HTML list
        'dropdownOptions' => [
            'label' => '<i class="glyphicon glyphicon-export"></i> Full'
        ],
    ]);*/

?>
<hr>

<?php

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false

        'export' => [
            'label' => 'Page',
            'fontAwesome' => true,
            'itemsAfter'=> [
                '<li role="presentation" class="divider"></li>',
                '<li class="dropdown-header">Export All Data</li>',
                $fullExportMenu
            ]
        ],


        'columns' => $gridColumns,

        'toolbar' =>  [
            ['content'=>
                //Html::button('<i class="glyphicon glyphicon-plus"></i>', ['type'=>'button', 'title'=>'Add Lead', 'class'=>'btn btn-success', 'onclick'=>'alert("This will launch the book creation form.\n\nDisabled for this demo!");']) . ' '.
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['leads/index'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
            ],
            //'{export}',
            $fullExportMenu,
            '{toggleData}'
        ],
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
        //'bordered' => true,
        'striped' => false,
        'condensed' => false,
        'responsive' => true,
        'hover' => true,
        'floatHeader' => true,
        'floatHeaderOptions' => ['scrollingTop' => 20],
        /*'showPageSummary' => true,*/
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Leads</h3>',
        ],

    ]); ?>

    <?//php Pjax::end(); ?>
</div>
