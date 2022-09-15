<?php

use common\models\Lead;
use src\access\ListsAccess;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Export Leads';
$this->params['breadcrumbs'][] = $this->title;
$lists =  new ListsAccess(Yii::$app->user->id);
?>
<style>
    .dropdown-menu {
        z-index: 1010;
    }
</style>
<div class="lead-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>

    <?php echo $this->render('_search', [
        'model' => $searchModel,
        'action' => 'export',
        'lists' => new ListsAccess()

    ]); ?>

    <p>
        <?php //= Html::a('Create Lead', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php

    $gridColumnsExport = [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'id',
        ],

        [
            'attribute' => 'uid',
        ],

        [
            'label' => 'Created',
            'attribute' => 'l_type_create',
            'value' => function (\common\models\Lead $model) {
                return Lead::TYPE_CREATE_LIST[$model->l_type_create] ?? '-';
            },
            'filter' => \common\models\Lead::TYPE_CREATE_LIST
        ],

        [
            'attribute' => 'status',
            'value' => function (\common\models\Lead $model) {
                return $model->getStatusName(false);
            },
            'filter' => \common\models\Lead::STATUS_LIST
        ],
        [
            'header' => 'Status Date',
            'value' => function (\common\models\Lead $model) {
                foreach ($model->leadFlows as $flow) {
                    if ($model->id === $flow['lead_id'] && $model->status === $flow['status']) {
                        return Yii::$app->formatter->asDatetime(strtotime($flow['created']), 'php: Y-m-d H:i');
                    }
                }
            }
        ],
        'reason' => [
            'header' => 'Trash/Reject reason.',
            'value' => function (\common\models\Lead $model) {
                if ($model->status === \common\models\Lead::STATUS_REJECT || $model->status === \common\models\Lead::STATUS_TRASH) {
                    return $model->getLastReasonFromLeadFlow();
                }
                return '';
            },
        ],
        [
            'attribute' => 'called_expert',
        ],
        [
            'attribute' => 'grade',
        ],
        [
            'attribute' => 'inCalls',
            'label' => 'In Calls',
        ],
        [
            'attribute' => 'inCallsDuration',
            'label' => 'In Calls Duration',
            'value' => static function (Lead $lead) {
                return $lead->inCallsDuration ?: '';
            },
        ],
        [
            'attribute' => 'outCalls',
            'label' => 'Out Calls',
        ],
        [
            'attribute' => 'outCallsDuration',
            'label' => 'Out Calls Duration',
            'value' => static function (Lead $lead) {
                return $lead->outCallsDuration ?: '';
            },
        ],
        [
            'attribute' => 'smsOffers',
            'label' => 'Sms Offers',
        ],
        [
            'attribute' => 'emailOffers',
            'label' => 'Email Offers',
        ],
        [
            'attribute' => 'quoteType',
            'label' => 'Quote Type',
            'value' => static function (Lead $lead) {
                return isset($lead->quoteType) ? ($lead->quoteType ? 'Agent' : 'Expert' ) : '';
            }
        ],
        [
            'header' => 'Segments',
            'value' => function (\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $segmentData = [];
                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        $segmentData[] = ($sk + 1) . '. ' . ($segment->origin . '->' . $segment->destination) . '';
                    }
                }

                $segmentStr = implode("\r\n", $segmentData);
                return '' . $segmentStr . '';
                //return $model->leadFlightSegmentsCount ? Html::a($model->leadFlightSegmentsCount, ['lead-flight-segment/index', "LeadFlightSegmentSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
            },

        ],

        [
            'header' => 'Origin City Code',
            'value' => function (\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $originCode = null;
                $destinationCode = null;

                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        if (!$originCode) {
                            $originCode = $segment->origin;
                            break;
                        }
                        //$destinationCode = $segment->destination;
                    }
                }

                return $originCode;
            },
        ],

        [
            'header' => 'Destination City Code',
            'value' => function (\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $originCode = null;
                $destinationCode = null;

                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        /*if(!$originCode) {
                            $originCode = $segment->origin;
                        }*/
                        if (!$destinationCode) {
                            $destinationCode = $segment->destination;
                        }
                    }
                }

                return $destinationCode;
            },
        ],

        [
            'header' => 'Origin City, full name',
            'value' => function (\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $originCode = null;
                $destinationCode = null;

                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        if (!$originCode) {
                            $originCode = $segment->origin;
                        }
                        //$destinationCode = $segment->destination;
                    }
                }

                $city = '-';

                $airport = \common\models\Airports::find()->where(['iata' => $originCode])->one();

                if ($airport && $airport->city) {
                    $city = $airport->city;
                }

                /*if($originCode) {
                    $airport = \common\models\AirportList::find()->where(['ai_iata_code' => $originCode])->one();
                    if($airport && $airport->aiRegionIsoCode) {
                        $city = $airport->aiRegionIsoCode->r_name;
                    }
                }*/

                return $city;
            },
        ],

        [
            'header' => 'Destination City, full name',
            'value' => function (\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $originCode = null;
                $destinationCode = null;

                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        /*if(!$originCode) {
                            $originCode = $segment->origin;
                        }*/
                        if (!$destinationCode) {
                            $destinationCode = $segment->destination;
                        }
                    }
                }

                $city = '-';

                $airport = \common\models\Airports::find()->where(['iata' => $destinationCode])->one();

                if ($airport && $airport->city) {
                    $city = $airport->city;
                }

                return $city;
            },
        ],

        [
            'header' => 'Origin Country',
            'value' => function (\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $originCode = null;
                $destinationCode = null;

                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        if (!$originCode) {
                            $originCode = $segment->origin;
                        }
                        // $destinationCode = $segment->destination;
                    }
                }

                $country = '-';
                if ($originCode) {
                    //$airport = \common\models\AirportList::find()->where(['ai_iata_code' => $originCode])->one();
                    $airport = \common\models\Airports::find()->where(['iata' => $originCode])->one();

                    if ($airport && $airport->a_country_code) {
                        $country = $airport->a_country_code;
                    }
                }

                return $country;
            },
        ],

        [
            'header' => 'Destination Country',
            'value' => function (\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $originCode = null;
                $destinationCode = null;

                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        /*if(!$originCode) {
                            $originCode = $segment->origin;
                        }*/
                        if (!$destinationCode) {
                            $destinationCode = $segment->destination;
                        }
                        //$destinationCode = $segment->destination;
                    }
                }

                $country = '-';
                if ($destinationCode) {
                    //$airport = \common\models\AirportList::find()->where(['ai_iata_code' => $destinationCode])->one();

                    $airport = \common\models\Airports::find()->where(['iata' => $destinationCode])->one();

                    if ($airport && $airport->a_country_code) {
                        $country = $airport->a_country_code;
                    }

                    /*if($airport && $airport->aiCountryIsoCode) {
                        $country = $airport->aiCountryIsoCode->c_iso_code;
                    }*/
                }

                return $country;
            },
        ],

        [
            'header' => 'Profit',
            'value' => function (\common\models\Lead $model) {
                $total = 0;

                if ($model->status == \common\models\Lead::STATUS_SOLD) {
                    $quote = \common\models\Quote::find()->where(['lead_id' => $model->id, 'status' => \common\models\Quote::STATUS_APPLIED])->orderBy(['id' => SORT_DESC])->one();

                    if (!$quote) {
                        $quote = \common\models\Quote::find()->where(['lead_id' => $model->id, 'status' => \common\models\Quote::STATUS_SENT])->orderBy(['id' => SORT_DESC])->one();
                    }

                    if ($quote) {
                        $prices = $quote->quotePrices;
                        if ($prices) {
                            foreach ($prices as $price) {
                                $total += (float)$price->selling - (float)$price->net;
                            }
                        }
                    }
                } else {
                    $total = '';
                }
                return $total;
            },
        ],

        [
            'header' => 'Quotes',
            'value' => static function (\common\models\Lead $model) {
                return $model->quotesCount ? $model->quotesCount  : '-';
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],

        [
            'header' => 'Expert Quotes',
            'value' => static function (\common\models\Lead $model) {
                return $model->quotesExpertCount ? $model->quotesExpertCount : '-';
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],
        [
            'header' => 'Outbound Date',
            'value' => function (\common\models\Lead $model) {
                $segments = $model->leadFlightSegments;
                $datetime = '';
                if (isset($segments[0]) && $segments[0]->departure) {
                    $datetime = date('d-M-Y', strtotime($segments[0]->departure));
                }
                return $datetime;
            },
        ],

        [
            'header' => 'Project info',
            'class' => \common\components\grid\project\ProjectColumn::class,
            'attribute' => 'project_id',
            'relation' => 'project',
        ],

        [
            'header' => 'Market info',
            'value' => function (\common\models\Lead $model) {
                return $model->source ? $model->source->name : '-';
            },
        ],


        [
            'attribute' => 'trip_type',
            'value' => function (\common\models\Lead $model) {
                return $model->getFlightTypeName();
            },
            'filter' => \common\models\Lead::getFlightTypeList()
        ],

        [
            'attribute' => 'cabin',
            'value' => static function (\common\models\Lead $model) {
                return $model->getCabinClassName();
            },
            'filter' => \common\models\Lead::CABIN_LIST
        ],

        [
            'attribute' => 'adults',
            'value' => function (\common\models\Lead $model) {
                return $model->adults ?: 0;
            },
            'filter' => array_combine(range(0, 9), range(0, 9)),
            'contentOptions' => ['class' => 'text-center'],
        ],

        [
            'attribute' => 'children',
            'value' => function (\common\models\Lead $model) {
                return $model->children ?: 0;
            },
            'filter' => array_combine(range(0, 9), range(0, 9)),
            'contentOptions' => ['class' => 'text-center'],
        ],

        [
            'attribute' => 'infants',
            'value' => function (\common\models\Lead $model) {
                return $model->infants ?: 0;
            },
            'filter' => array_combine(range(0, 9), range(0, 9)),
            'contentOptions' => ['class' => 'text-center'],
        ],

        [
            'header' => 'Agent name',
            'value' => function (\common\models\Lead $model) {
                return $model->employee_id ? $model->employee->username : '';
            },
        ],
        [
            'header' => 'Created Date',
            'value' => function (\common\models\Lead $model) {
                return Yii::$app->formatter->asDate($model->created);
            },
        ],

        [
            'header' => 'Created Time',
            'value' => function (\common\models\Lead $model) {
                return Yii::$app->formatter->asDatetime(strtotime($model->created), 'php:H:i');
            },
        ],
    ];

    unset($gridColumnsExport['reason']);

    Yii::$app->state = Yii::$app::STATE_END;

    $pdfHeader = [
        'L'    => [
            'content' => 'Sales Engine',
        ],
        'C'    => [
            'content' => 'LEADS REPORT',
        ],
        'R'    => [
            'content' => 'Generated: ' . date('Y-m-d H:i'),
        ],
        'line' => true,
    ];

    $pdfFooter = [
        'L'    => [
            'content'     => '',
        ],
        'C'    => [
            'content' => '',
        ],
        'line' => false,
    ];

    $fullExportMenu = ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumnsExport,
        'exportConfig' => [
            ExportMenu::FORMAT_PDF => [
                'pdfConfig' => [
                    'mode' => 'c',
                    'format' => 'A4-L',
                    /*'methods' => [
                        'SetHeader' => ['Test Export'],
                        'SetFooter' => ['{PAGENO}']
                    ],*/
                ]
            ]
        ],
        'fontAwesome' => true,
        //'stream' => false, // this will automatically save file to a folder on web server
        //'deleteAfterSave' => false, // this will delete the saved web file after it is streamed to browser,
        //'batchSize' => 10,
        'target' => \kartik\export\ExportMenu::TARGET_BLANK,
        'linkPath' => '/assets/',
        'folder' => '@webroot/assets', // this is default save folder on server
        'dropdownOptions' => [
            'label' => 'Full Export'
        ],
        'columnSelectorOptions' => [
            'label' => 'Export Fields'
        ],
    ]);

    ?>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false
        'export' => [
            'label' => 'Page',
            'fontAwesome' => true,
            /*'itemsAfter'=> [
                '<li role="presentation" class="divider"></li>',
                '<li class="dropdown-header">Export All Data</li>',
                $fullExportMenu
            ]*/
        ],

        'exportConfig' => [
            'html' => [],
            'csv' => [],
            'txt' => [],
            'xls' => [],
            'pdf' => [
                'config' => [
                    'mode' => 'c',
                    'options' => [
                        'title' => 'Exported Leads in PDF',
                    ],
                    'methods' => [
                        'SetHeader' => [
                            ['odd' => $pdfHeader, 'even' => $pdfHeader],
                        ],
                        'SetFooter' => [
                            ['odd' => $pdfFooter, 'even' => $pdfFooter],
                        ],
                    ],
                ]
            ],
            'json' => [],
        ],

        'columns' => $gridColumnsExport,
        'toolbar' =>  [
            ['content' =>
            //Html::button('<i class="glyphicon glyphicon-plus"></i>', ['type'=>'button', 'title'=>'Add Lead', 'class'=>'btn btn-success', 'onclick'=>'alert("This will launch the book creation form.\n\nDisabled for this demo!");']) . ' '.
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['leads/export'], ['data-pjax' => 0, 'class' => 'btn btn-outline-secondary', 'title' => 'Reset Grid']) . ' ' .
                '{export}' . ' ' .
                Html::button('<i class="glyphicon glyphicon-download"></i> Full Export', ['type' => 'button', 'title' => 'Full Export in CSV', 'id' => 'fullExportLeads', 'class' => 'btn btn-outline-secondary'])
            ],
            //$fullExportMenu,
            //'{toggleData}'
        ],
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container'], 'style' => 'overflow: auto;'],
        //'bordered' => true,
        'striped' => true,
        'condensed' => true,
        'responsive' => false,
        'hover' => true,
        'floatHeader' => false,
        //'floatHeaderOptions' => ['scrollingTop' => 20],
        //'showPageSummary' => true,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Export Leads</h3>',
            'before' => '<span class="badge badge-secondary">* Limitation for Full Export is max 50 000 items</span>'
        ],

    ]); ?>

    <?php Pjax::end(); ?>
</div>

<?php
$downloadButton = Html::a('<i class="glyphicon glyphicon-cloud-download"></i> Download', ['leads/download-csv'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => 'Download']);
yii\bootstrap4\Modal::begin([
    'id' => 'modalClient',
    'size' => \yii\bootstrap4\Modal::SIZE_DEFAULT,
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);
echo "<div id='modalClientContent'></div>";
yii\bootstrap4\Modal::end();

$jsCode = <<<JS
    $(document).on('click', '#fullExportLeads', function(){
        var button = '$downloadButton';
        //e.preventDefault();
        $('#modalClient').modal('show').find('#modalClientContent').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Processing ...</div>');
        $('#modalClient-label').html($(this).attr('title'));
       
        var params = {};
	    var parser = document.createElement('a');
	    parser.href = window.location.href;
	    var query = parser.search.substring(1);       
        
        $.ajax({
            url: 'export-csv?' + query,
            type: 'GET',            
             success: function(data) {
                 //$('#modalClient').find('#modalClientContent').html(data);
                  $('#modalClient').find('#modalClientContent').html('<div class="container"> <div class="row"> <div class="col text-center">' + button + '</div></div></div>');
             },
             error: function(error) {                   
                //console.log('Error code: ' + error.status);
                if(error.status == 504){
                    $.ajax({
                        url: 'file-size',   
                        success: function(data) {
                             $('#modalClient').find('#modalClientContent').html('<div class="container"> <div class="row"> <div class="col text-center">' + button + '</div></div></div>');
                        },                
                    })
                } else {
                     $('#modalClient').find('#modalClientContent').html('Error code: ' + error.status);
                }
             }
         });        
       return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
