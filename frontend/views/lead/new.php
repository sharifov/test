<?php

use sales\formatters\client\ClientTimeFormatter;
use yii\helpers\Html;
use yii\widgets\Pjax;
//use kartik\grid\GridView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'New Queue';

$this->params['breadcrumbs'][] = $this->title;
?>

<h1>
    <i class="fa fa-briefcase"></i> <?=\yii\helpers\Html::encode($this->title)?>
</h1>

<div class="lead-new">



    <?php Pjax::begin(); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>

    <?php

    $gridColumns = [
        [
            'attribute' => 'id',
            'label' => 'Lead ID',
            'value' => static function (\common\models\Lead $model) {
                return $model->id;
            },
            'options' => [
                'style' => 'width:80px'
            ]
        ],

        [
            'class' => \common\components\grid\project\ProjectColumn::class,
            'attribute' => 'project_id',
            'relation' => 'project'
        ],

        /*[
            'attribute' => 'project_id',
            'value' => static function (\common\models\Lead $model) {
                return $model->project ? '<span class="badge badge-info">' . $model->project->name . '</span>' : '-';
            },
            'format' => 'raw',
            'options' => [
                'style' => 'width:120px'
            ],
            'filter' => \common\models\Project::getList(),
        ],*/

        [
            'attribute' => 'source_id',
            'value' => function(\common\models\Lead $model) {
                return $model->source ? $model->source->name : '-';
            },
            'filter' => \common\models\Sources::getList(true)
        ],

        [
            'attribute' => 'pending',
            'label' => 'Pending Time',
            'value' => static function (\common\models\Lead $model) {

                $createdTS = strtotime($model->created);

                $diffTime = time() - $createdTS;
                $diffHours = (int) ($diffTime / (60 * 60));


                $str = ($diffHours > 3 && $diffHours < 73 ) ? $diffHours.' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                $str .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));

                return $str;
            },
            'options' => [
                'style' => 'width:160px'
            ],
            'format' => 'raw'
        ],

        [
            // 'attribute' => 'client_id',
            'header' => 'Request',
            'format' => 'raw',
            'value' => static function (\common\models\Lead $model) {

                $clientName = trim($model->l_client_first_name . ' ' . $model->l_client_last_name);

                if ($clientName) {
                    $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName).'';
                }

                $str = $model->l_client_email ? '<br><i class="fa fa-envelope"></i> ' . $model->l_client_email : '';
                $str .= $model->l_client_phone ? '<br><i class="fa fa-phone"></i>' . $model->l_client_phone : '';
                $clientName .= $str;

                return $clientName;
            },

            'options' => [
                'style' => 'width:160px'
            ]
        ],

        [
            // 'attribute' => 'client_id',
            'header' => 'Client',
            'format' => 'raw',
            'value' => static function (\common\models\Lead $model) {

                if ($model->client) {

                    $clientName = trim($model->client->first_name . ' ' . $model->client->last_name);

                    if($clientName === 'ClientName') {
                        $clientName = '-';
                    }

                    if ($clientName) {
                        $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName).'';
                    }

                    $str = $model->client->clientEmails ? '<br><i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')) . '' : '';
                    $str .= $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) . '' : '';

                    $clientName .= $str;
                } else {
                    $clientName = '-';
                }

                return $clientName;
            },
            //'visible' => ! $isAgent,
            'options' => [
                'style' => 'width:160px'
            ]
        ],


        /*[
            'attribute' => 'client.name',
            'header' => 'Client name',
            'format' => 'raw',
            'value' => function(\common\models\Lead $model) {
                if($model->client) {
                    $clientName = $model->client->first_name . ' ' . $model->client->last_name;
                    if ($clientName === 'Client Name') {
                        $clientName = '- - - ';
                    } else {
                        $clientName = '<i class="fa fa-user"></i> '. Html::encode($clientName);
                    }
                } else {
                    $clientName = '-';
                }

                return $clientName;
            },
            'options' => ['style' => 'width:160px'],
            //'filter' => \common\models\Employee::getList()
        ],*/

        'client_id:client',

        /*[
            'attribute' => 'client_id',
            'value' => static function (\common\models\Lead $model) {
                return $model->client_id ? Html::a($model->client_id, ['client/view', 'id' => $model->client_id], ['data-pjax' => 0, 'target' => '_blank']) : '-';
            },
            'format' => 'raw',
        ],*/

        /*[
            'attribute' => 'client.phone',
            //'header' => 'Client Phones',
            'format' => 'raw',
            'value' => function(\common\models\Lead $model)
            {
                $str = null;
                if($model->client && $model->client->clientPhones) {
                    $str = '<i class="fa fa-phone"></i> ' . implode('<br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone'));
                }
                return $str ?? '-';
            },
            'options' => ['style' => 'width:180px'],
        ],*/

        [
            'header' => 'Client time',
            'format' => 'raw',
            'value' => function(\common\models\Lead $model) {
                return ClientTimeFormatter::format($model->getClientTime2(), $model->offset_gmt);
            },
            'options' => ['style' => 'width:90px'],
        ],

        [
            'header' => 'Location',
            'format' => 'raw',
            'value' => function(\common\models\Lead $model) {
                // {"ipAddress":"71.150.186.68","countryCode":"US","countryName":"United States","regionName":"Kentucky","cityName":"Lowmansville","zipCode":"41232","latitude":"38.0001","longitude":"-82.7151","timeZone":"-04:00"}
                $str = '';
                if($model->request_ip_detail) {
                    $ipData = @json_decode($model->request_ip_detail, true);
                    $location = [];
                    if($ipData) {
                        $location[] = $ipData['countryCode'] ?? '';
                        //$location[] = $ipData['countryName'] ?? '';
                        $location[] = $ipData['regionName'] ?? '';
                        $location[] = $ipData['cityName'] ?? '';
                    }
                    $str = implode(', ', $location);
                }
                return $str ?: '-';
            },
            'options' => ['style' => 'width:90px'],
        ],




        [
            //'attribute' => 'Quotes',
            'label' => 'Calls',
            'value' => static function (\common\models\Lead $model) {
                $cnt = $model->getCountCalls();
                return $cnt ?: '-';
            },
            'options' => [
                'style' => 'width:60px'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ],
            //'format' => 'raw'
        ],


        [
            'header' => 'Depart',
            'value' => static function (\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;

                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        return date('d-M-Y', strtotime($segment->departure));
                    }
                }
                return '-';

            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'options' => [
                'style' => 'width:100px'
            ]
        ],

        [
            'header' => 'Segments',
            'value' => static function (\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $segmentData = [];
                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        $segmentData[] = ($sk + 1) . '. <small>' . $segment->origin . ' <i class="fa fa-long-arrow-right"></i> ' . $segment->destination . '</small>';
                    }
                }

                $segmentStr = implode('<br>', $segmentData);
                return '' . $segmentStr . '';
                // return $model->leadFlightSegmentsCount ? Html::a($model->leadFlightSegmentsCount, ['lead-flight-segment/index', "LeadFlightSegmentSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
            },
            'format' => 'raw',
            //'visible' => ! $isAgent,
            'contentOptions' => [
                'class' => 'text-left'
            ],
            'options' => [
                'style' => 'width:140px'
            ]
        ],

        [
            'label' => 'Pax',
            'value' => static function (\common\models\Lead $model) {
                return '<span title="adult"><i class="fa fa-male"></i> '. $model->adults .'</span> / <span title="child"><i class="fa fa-child"></i> ' . $model->children . '</span> / <span title="infant"><i class="fa fa-info"></i> ' . $model->infants.'</span>';
            },
            'format' => 'raw',
            //'visible' => ! $isAgent,
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'options' => [
                'style' => 'width:100px'
            ]
        ],


        [
            'attribute' => 'cabin',
            'value' => static function (\common\models\Lead $model) {
                return $model->getCabinClassName();
            },
            'filter' => \common\models\Lead::CABIN_LIST
        ],


        [
            'attribute' => 'l_call_status_id',
            'value' => static function (\common\models\Lead $model) {
                return \common\models\Lead::CALL_STATUS_LIST[$model->l_call_status_id] ?? '-';
            },
            'filter' => \common\models\Lead::CALL_STATUS_LIST
        ],

        [
            'attribute' => 'request_ip',
            'value' => static function (\common\models\Lead $model) {
                return $model->request_ip;
            },
        ],

        [
            'attribute' => 'l_pending_delay_dt',
            'value' => static function (\common\models\Lead $model) {
                return $model->l_pending_delay_dt ? Yii::$app->formatter->asDatetime(strtotime($model->l_pending_delay_dt)) : '-';
            },
        ],

        /*[
            'attribute' => 'l_request_hash',
            'value' => static function (\common\models\Lead $model) {
                return $model->l_request_hash ?: '-';
            },
        ],*/

        [
            'attribute' => 'employee_id',
            'format' => 'raw',
            'value' => static function (\common\models\Lead $model) {
                return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
            },
            'filter' => false //\common\models\Employee::getList()
        ],

        [
            //'attribute' => 'l_request_hash',
            'label' => 'Duplicate',
            'value' => static function (\common\models\Lead $model) {
                return $model->leads0 ? Html::a(count($model->leads0), ['lead/duplicate', 'LeadSearch[l_request_hash]' => $model->l_request_hash], ['data-pjax' => 0, 'target' => '_blank']) : '-';
            },
            'format' => 'raw',
        ],

        [
            'attribute' => 'l_init_price',
            //'format' => 'raw',
            'value' => function(\common\models\Lead $model) {
                return $model->l_init_price ? number_format($model->l_init_price, 2) . ' $': '-';
            },
            'contentOptions' => [
                'class' => 'text-right'
            ],
        ],

		[
			'label' => 'Is Test',
			'attribute' => 'l_is_test',
			'value' => static function (\common\models\Lead $model) {
				if ($model->l_is_test) {
					$label = '<label class="label label-success">True</label>';
				} else {
					$label = '<label class="label label-danger">False</label>';
				}
				return $label;
			},
			'options' => [
				'style' => 'width:180px'
			],
			'format' => 'raw',
			'filter' => [
				1 => 'True',
				0 => 'False'
			]
		],

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{action}',
            'buttons' => [
                'action' => function ($url, \common\models\Lead $model, $key) {
                    $buttons = '';

                    $buttons .= Html::a('<i class="fa fa-search"></i> View', ['lead/view', 'gid' => $model->gid], [
                            'class' => 'btn btn-info btn-xs',
                            'data-pjax' => 0,
                            'target' => '_blank',
                        ]);

                    $buttons .= ' '. Html::a('<i class="fa fa-list-ul"></i> View', ['leads/view', 'id' => $model->id], [
                            'class' => 'btn btn-warning btn-xs',
                            'data-pjax' => 0,
                            'target' => '_blank',
                        ]);

                    return $buttons;
                }
            ]
        ]
    ];

    ?>
<?php
echo GridView::widget([

    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
]);
?>
<?php Pjax::end(); ?>
</div>

<?php
