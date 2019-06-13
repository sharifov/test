<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Export Leads';
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
.dropdown-menu {
    z-index: 1010;
}
</style>
<div class="lead-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel, 'action' => 'export']); ?>



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
            'filter' => \common\models\Lead::STATUS_LIST
        ],
        'reason' => [
            'header' => 'Trash/Reject reason.',
            'value' => function(\common\models\Lead $model) {
                if ($model->status === \common\models\Lead::STATUS_REJECT || $model->status === \common\models\Lead::STATUS_TRASH) {
                    return $model->getLastReason();
                }
                return '';
            },
        ],
        [
            'header' => 'Customer email',
            'value' => function(\common\models\Lead $model) {
                $emails = [];
                if ($model->client && $emailList = $model->client->clientEmails) {
                    $emails = \yii\helpers\ArrayHelper::map($emailList, 'email', 'email');
                }
                return implode(', ', $emails);
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
            'value' => function(\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $originCode = null;
                $destinationCode = null;

                if($segments) {
                    foreach ($segments as $sk => $segment) {

                        /*if(!$originCode) {
                            $originCode = $segment->origin;
                        }*/
                        if(!$destinationCode) {
                            $destinationCode = $segment->destination;
                        }

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
                        //$destinationCode = $segment->destination;
                    }
                }

                $city = '-';

                $airport = \common\models\Airport::find()->where(['iata' => $originCode])->one();

                if($airport && $airport->city) {
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
            'value' => function(\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $originCode = null;
                $destinationCode = null;

                if($segments) {
                    foreach ($segments as $sk => $segment) {

                        /*if(!$originCode) {
                            $originCode = $segment->origin;
                        }*/
                        if(!$destinationCode) {
                            $destinationCode = $segment->destination;
                        }
                    }
                }

                $city = '-';

                $airport = \common\models\Airport::find()->where(['iata' => $destinationCode])->one();

                if($airport && $airport->city) {
                    $city = $airport->city;
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
                        // $destinationCode = $segment->destination;
                    }
                }

                $country = '-';
                if($originCode) {
                    //$airport = \common\models\AirportList::find()->where(['ai_iata_code' => $originCode])->one();
                    $airport = \common\models\Airport::find()->where(['iata' => $originCode])->one();

                    if($airport && $airport->countryId) {
                        $country = $airport->countryId;
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

                        /*if(!$originCode) {
                            $originCode = $segment->origin;
                        }*/
                        if(!$destinationCode) {
                            $destinationCode = $segment->destination;
                        }
                        //$destinationCode = $segment->destination;
                    }
                }

                $country = '-';
                if($destinationCode) {
                    //$airport = \common\models\AirportList::find()->where(['ai_iata_code' => $destinationCode])->one();

                    $airport = \common\models\Airport::find()->where(['iata' => $destinationCode])->one();

                    if($airport && $airport->countryId) {
                        $country = $airport->countryId;
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
            'value' => function(\common\models\Lead $model) {
                $total = 0;

                if($model->status == \common\models\Lead::STATUS_SOLD) {
                    $quote = \common\models\Quote::find()->where(['lead_id' => $model->id, 'status' => \common\models\Quote::STATUS_APPLIED])->orderBy(['id' => SORT_DESC])->one();

                    if (!$quote) {
                        $quote = \common\models\Quote::find()->where(['lead_id' => $model->id, 'status' => \common\models\Quote::STATUS_SEND])->orderBy(['id' => SORT_DESC])->one();
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
            'value' => function (\common\models\Lead $model) {
                return $model->quotesCount ? $model->quotesCount  : '-';
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ]
            ],

            [
                'header' => 'Expert Quotes',
                'value' => function (\common\models\Lead $model) {
                return $model->quotesExpertCount ? $model->quotesExpertCount: '-';
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ],
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
            'header' => 'Project info',
            'value' => function(\common\models\Lead $model) {
                return $model->project ? $model->project->name : '-';
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
                return $model->getFlightTypeName();
            },
            'filter' => \common\models\Lead::getFlightTypeList()
        ],

        [
            'attribute' => 'cabin',
            'value' => function (\common\models\Lead $model) {
                return $model->getCabinClassName();
            },
            'filter' => \common\models\Lead::CABIN_LIST
        ],

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
                return $model->children ?: 0;
            },
            'filter' => array_combine(range(0, 9), range(0, 9)),
            'contentOptions' => ['class' => 'text-center'],
        ],

        [
            'attribute' => 'infants',
            'value' => function(\common\models\Lead $model) {
                return $model->infants ?: 0;
            },
            'filter' => array_combine(range(0, 9), range(0, 9)),
            'contentOptions' => ['class' => 'text-center'],
        ],

        [
            'header' => 'Agent name',
            'value' => function(\common\models\Lead $model) {
                return $model->employee_id ? $model->employee->username : '';
            },
        ],
        [
            'header' => 'Created Date',
            'value' => function(\common\models\Lead $model) {
                return Yii::$app->formatter->asDate($model->created);
            },
        ],

        [
            'header' => 'Created Time',
            'value' => function(\common\models\Lead $model) {
                return Yii::$app->formatter->asDatetime(strtotime($model->created), 'php:H:i');
            },
        ],



    ];


        Yii::$app->state = Yii::$app::STATE_END;



        $fullExportMenu = \kartik\export\ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumnsExport,
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
            ]
        ]);

        unset($gridColumnsExport['reason']);

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

<?php

    echo GridView::widget([
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


        'columns' => $gridColumnsExport,

        'toolbar' =>  [
            ['content'=>
                //Html::button('<i class="glyphicon glyphicon-plus"></i>', ['type'=>'button', 'title'=>'Add Lead', 'class'=>'btn btn-success', 'onclick'=>'alert("This will launch the book creation form.\n\nDisabled for this demo!");']) . ' '.
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['leads/index'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
            ],
            '{export}',
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
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Export Leads</h3>',
        ],

    ]); ?>

    <?php Pjax::end(); ?>
</div>
