<?php

use modules\lead\src\abac\queue\LeadQueueBusinessInboxAbacDto;
use modules\lead\src\abac\queue\LeadQueueBusinessInboxAbacObject;
use src\formatters\client\ClientTimeFormatter;
use src\model\client\helpers\ClientFormatter;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
use common\models\Lead;
use src\auth\Auth;

/**
 * @var $this yii\web\View
 * @var $searchModel common\models\search\LeadSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = 'Business Inbox Queue';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><i class="fa fa-ioxhost text-info"></i> <?=\yii\helpers\Html::encode($this->title)?></h1>

<div class="lead-business-inbox">

    <?php Pjax::begin(['timeout' => 5000, 'scrollTo' => 0]); ?>

    <?php
    $gridColumns = [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'id',
            'label' => 'Lead ID',
            'value' => static function (Lead $model) {
                return $model->id;
            },
            'options' => [
                'style' => 'width:80px'
            ]
        ],

        [
            'attribute' => 'l_type',
            'value' => static function (Lead $model) {
                return $model->l_type ? '<span class="label label-default" style="font-size: 13px">' . $model::TYPE_LIST[$model->l_type] . '</span>' : ' - ';
            },
            'format' => 'raw',
            'filter' => Lead::TYPE_LIST,
        ],

        [
            'class' => \common\components\grid\project\ProjectColumn::class,
            'attribute' => 'project_id',
            'relation' => 'project'
        ],
        [
            'attribute' => 'source_id',
            'value' => function (Lead $model) {
                return $model->source ? $model->source->name : '-';
            },
            'filter' => \common\models\Sources::getList(true),
            /** @abac LeadQueueBusinessInboxAbacObject::UI_QUEUE_COLUMN, LeadQueueBusinessInboxAbacObject::ACTION_COLUMN_SOURCE_ID, Show source_id */
            'visible' => \Yii::$app->abac->can(
                null,
                LeadQueueBusinessInboxAbacObject::UI_QUEUE_COLUMN,
                LeadQueueBusinessInboxAbacObject::ACTION_COLUMN_SOURCE_ID
            ),
        ],
        [
            'attribute' => 'pending',
            'label' => 'Pending Time',
            'value' => static function (Lead $model) {

                $createdTS = strtotime($model->created);

                $diffTime = time() - $createdTS;
                $diffHours = (int) ($diffTime / (60 * 60));

                $str = ($diffHours > 3 && $diffHours < 73 ) ? $diffHours . ' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                $str .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));

                return $str;
            },
            'options' => [
                'style' => 'width:160px'
            ],
            'format' => 'raw',
            /** @abac LeadQueueBusinessInboxAbacObject::UI_QUEUE_COLUMN, LeadQueueBusinessInboxAbacObject::ACTION_COLUMN_PENDING_TIME, Show pending */
            'visible' => \Yii::$app->abac->can(
                null,
                LeadQueueBusinessInboxAbacObject::UI_QUEUE_COLUMN,
                LeadQueueBusinessInboxAbacObject::ACTION_COLUMN_PENDING_TIME
            ),
        ],

        [
            'header' => 'Request',
            'format' => 'raw',
            'value' => static function (Lead $model) {

                $clientName = trim($model->l_client_first_name . ' ' . $model->l_client_last_name);

                if ($clientName) {
                    $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName) . '';
                }
                $str = '';
                //$str = $model->l_client_email ? '<br><i class="fa fa-envelope"></i> ' . $model->l_client_email : '';
                //$str .= $model->l_client_phone ? '<br><i class="fa fa-phone"></i>' . $model->l_client_phone : '';
                $clientName .= $str;

                return $clientName;
            },

            'options' => [
                'style' => 'width:160px'
            ]
        ],

        [
            'header' => 'Client',
            'format' => 'raw',
            'value' => static function (Lead $model) {

                if ($model->client) {
                    $clientName = trim($model->client->first_name . ' ' . $model->client->last_name);

                    if ($clientName === 'ClientName') {
                        $clientName = '-';
                    }

                    if ($clientName) {
                        $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName) . '';
                    }

                    if ($model->client->isExcluded()) {
                        $clientName = ClientFormatter::formatExclude($model->client)  . $clientName;
                    }

                    $str = '';

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
        'client_id:client',
        [
            'header' => 'Client time',
            'format' => 'raw',
            'value' => function (Lead $model) {
                return ClientTimeFormatter::format($model->getClientTime2(), $model->offset_gmt);
            },
            'options' => ['style' => 'width:90px'],
            /** @abac LeadQueueBusinessInboxAbacObject::UI_QUEUE_COLUMN, LeadQueueBusinessInboxAbacObject::ACTION_COLUMN_CLIENT_TIME, Show Client time */
            'visible' => \Yii::$app->abac->can(
                null,
                LeadQueueBusinessInboxAbacObject::UI_QUEUE_COLUMN,
                LeadQueueBusinessInboxAbacObject::ACTION_COLUMN_CLIENT_TIME
            ),
        ],
        [
            'header' => 'Location',
            'format' => 'raw',
            'value' => function (Lead $model) {
                $str = '';
                if ($model->request_ip_detail) {
                    $ipData = @json_decode($model->request_ip_detail, true);
                    $location = [];
                    if ($ipData) {
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
            /** @abac LeadQueueBusinessInboxAbacObject::UI_QUEUE_COLUMN, LeadQueueBusinessInboxAbacObject::ACTION_COLUMN_LOCATION, Show Location */
            'visible' => \Yii::$app->abac->can(
                null,
                LeadQueueBusinessInboxAbacObject::UI_QUEUE_COLUMN,
                LeadQueueBusinessInboxAbacObject::ACTION_COLUMN_LOCATION
            ),
        ],

        [
            'label' => 'Calls',
            'value' => static function (Lead $model) {
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
            'value' => static function (Lead $model) {

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
            ],
            /** @abac LeadQueueBusinessInboxAbacObject::UI_QUEUE_COLUMN, LeadQueueBusinessInboxAbacObject::ACTION_COLUMN_DEPART, Show Depart */
            'visible' => \Yii::$app->abac->can(
                null,
                LeadQueueBusinessInboxAbacObject::UI_QUEUE_COLUMN,
                LeadQueueBusinessInboxAbacObject::ACTION_COLUMN_DEPART
            ),
        ],

        [
            'header' => 'Segments',
            'value' => static function (Lead $model) {

                $segments = $model->leadFlightSegments;
                $segmentData = [];
                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        $segmentData[] = ($sk + 1) . '. <small>' . $segment->origin . ' <i class="fa fa-long-arrow-right"></i> ' . $segment->destination . '</small>';
                    }
                }

                $segmentStr = implode('<br>', $segmentData);
                return '' . $segmentStr . '';
            },
            'format' => 'raw',
            //'visible' => ! $isAgent,
            'contentOptions' => [
                'class' => 'text-left'
            ],
            'options' => [
                'style' => 'width:140px'
            ],
            /** @abac LeadQueueBusinessInboxAbacObject::UI_QUEUE_COLUMN, LeadQueueBusinessInboxAbacObject::ACTION_COLUMN_SEGMENTS, Show Segments */
            'visible' => \Yii::$app->abac->can(
                null,
                LeadQueueBusinessInboxAbacObject::UI_QUEUE_COLUMN,
                LeadQueueBusinessInboxAbacObject::ACTION_COLUMN_SEGMENTS
            ),
        ],

        [
            'label' => 'Pax',
            'value' => static function (Lead $model) {
                return '<span title="adult"><i class="fa fa-male"></i> ' . $model->adults . '</span> / <span title="child"><i class="fa fa-child"></i> ' . $model->children . '</span> / <span title="infant"><i class="fa fa-info"></i> ' . $model->infants . '</span>';
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
            'value' => static function (Lead $model) {
                return $model->getCabinClassName();
            },
            'filter' => Lead::CABIN_LIST
        ],

        [
            'attribute' => 'l_call_status_id',
            'value' => static function (Lead $model) {
                return Lead::CALL_STATUS_LIST[$model->l_call_status_id] ?? '-';
            },
            'filter' => Lead::CALL_STATUS_LIST
        ],

        [
            'attribute' => 'request_ip',
            'value' => static function (Lead $model) {
                return $model->request_ip;
            },
            /** @abac LeadQueueBusinessInboxAbacObject::UI_QUEUE_COLUMN, LeadQueueBusinessInboxAbacObject::ACTION_COLUMN_REQUEST_IP, Show request_ip */
            'visible' => \Yii::$app->abac->can(
                null,
                LeadQueueBusinessInboxAbacObject::UI_QUEUE_COLUMN,
                LeadQueueBusinessInboxAbacObject::ACTION_COLUMN_REQUEST_IP
            ),
        ],

        [
            'attribute' => 'l_pending_delay_dt',
            'value' => static function (Lead $model) {
                return $model->l_pending_delay_dt ? Yii::$app->formatter->asDatetime(strtotime($model->l_pending_delay_dt)) : '-';
            },
        ],

        [
            'attribute' => 'employee_id',
            'format' => 'raw',
            'value' => static function (Lead $model) {
                return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
            },
            'filter' => false //\common\models\Employee::getList()
        ],

        [
            'label' => 'Duplicate',
            'value' => static function (Lead $model) {
                return $model->leads0 ? Html::a(count($model->leads0), ['lead/duplicate', 'LeadSearch[l_request_hash]' => $model->l_request_hash], ['data-pjax' => 0, 'target' => '_blank']) : '-';
            },
            'format' => 'raw',
        ],

        [
            'attribute' => 'l_init_price',
            //'format' => 'raw',
            'value' => function (Lead $model) {
                return $model->l_init_price ? number_format($model->l_init_price, 2) . ' $' : '-';
            },
            'contentOptions' => [
                'class' => 'text-right'
            ],
        ],

        [
            'label' => 'Is Test',
            'attribute' => 'l_is_test',
            'value' => static function (Lead $model) {
                if ($model->l_is_test) {
                    $label = '<label class="label label-success">True</label>';
                } else {
                    $label = '<label class="label label-danger">False</label>';
                }
                return $label;
            },
            'options' => [
                'style' => 'width:75px'
            ],
            'format' => 'raw',
            'filter' => [
                1 => 'True',
                0 => 'False'
            ]
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{take} <br> {view}',
            'visibleButtons' => [
                /*'take' => static function (Lead $model, $key, $index) {
                    return Auth::can('lead/view', ['lead' => $model]);
                },*/
                'view' => static function (Lead $model, $key, $index) {
                    /** @abac LeadQueueBusinessInboxAbacObject::UI_BUTTON_VIEW, LeadQueueBusinessInboxAbacObject::ACTION_READ, Show view button */
                    $leadQueueBusinessInboxAbacDto = new LeadQueueBusinessInboxAbacDto($model, Auth::id());
                    $abacCan = \Yii::$app->abac->can(
                        $leadQueueBusinessInboxAbacDto,
                        LeadQueueBusinessInboxAbacObject::UI_BUTTON_VIEW,
                        LeadQueueBusinessInboxAbacObject::ACTION_READ
                    );
                    $authCan = Auth::can('lead/view', ['lead' => $model]);
                    return ($abacCan && $authCan);
                },
            ],
            'buttons' => [
                'take' => static function ($url, Lead $model) {
                    return Html::a('<i class="fa fa-download"></i> Take', [
                            'lead/take',
                        'gid' => $model->gid
                    ], [
                        'class' => 'btn btn-primary btn-xs take-processing-btn',
                        'data-pjax' => 0,
                    ]);
                },
                'view' => static function ($url, Lead $model) {
                    return Html::a('<i class="glyphicon glyphicon-search"></i> View', [
                        'lead/view',
                        'gid' => $model->gid
                    ], [
                        'class' => 'btn btn-info btn-xs',
                        'target' => '_blank',
                        'data-pjax' => 0,
                        'title' => 'View',
                    ]);
                }
            ],
        ]
    ];

    ?>
<?php
echo GridView::widget([

    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'rowOptions' => function (Lead $model) {
        if ($model->l_pending_delay_dt && time() < strtotime($model->l_pending_delay_dt)) {
            return ['class' => 'danger'];
        }

        if (!$model->l_client_time && (time() - strtotime($model->created)) > (Lead::PENDING_ALLOW_CALL_TIME_MINUTES * 60)) {
            return ['class' => 'danger'];
        }
    }
]);
?>
<?php Pjax::end(); ?>
</div>
