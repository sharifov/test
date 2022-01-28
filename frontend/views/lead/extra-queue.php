<?php

use yii\grid\ActionColumn;
use src\auth\Auth;
use src\formatters\client\ClientTimeFormatter;
use yii\helpers\Html;
use yii\widgets\Pjax;
//use kartik\grid\GridView;
use yii\grid\GridView;
use common\models\Lead;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Extra Queue';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1>
    <i class="fa fa-history"></i> <?=\yii\helpers\Html::encode($this->title)?>
</h1>

<div class="lead-new">

    <?php Pjax::begin(['scrollTo' => 0]); ?>

    <?php
    $gridColumns = [
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
            'filter' =>  Lead::TYPE_LIST,
        ],

        [
            'class' => \common\components\grid\project\ProjectColumn::class,
            'attribute' => 'project_id',
            'relation' => 'project'
        ],
        [
            'attribute' => 'source_id',
            'value' => function (\common\models\Lead $model) {
                return $model->source ? $model->source->name : '-';
            },
            'filter' => \common\models\Sources::getList(true)
        ],
        [
            'header' => 'Client',
            'format' => 'raw',
            'value' => static function (\common\models\Lead $model) {
                if ($model->client) {
                    $clientName = trim($model->client->first_name . ' ' . $model->client->last_name);

                    if ($clientName === 'ClientName') {
                        $clientName = '-';
                    }
                    if ($clientName) {
                        $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName) . '';
                    }
                } else {
                    $clientName = '-';
                }
                return $clientName;
            },
            'options' => [
                'style' => 'width:160px'
            ]
        ],
        [
            'header' => 'Client time',
            'format' => 'raw',
            'value' => function (\common\models\Lead $model) {
                return ClientTimeFormatter::format($model->getClientTime2(), $model->offset_gmt);
            },
            'options' => ['style' => 'width:90px'],
        ],
        [
            'header' => 'Location',
            'format' => 'raw',
            'value' => function (\common\models\Lead $model) {
                $str = '';
                if ($model->request_ip_detail) {
                    $ipData = @json_decode($model->request_ip_detail, true);
                    $location = [];
                    if ($ipData) {
                        $location[] = $ipData['countryCode'] ?? '';
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
            },
            'format' => 'raw',
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
                return '<span title="adult"><i class="fa fa-male"></i> ' . $model->adults . '</span> / <span title="child"><i class="fa fa-child"></i> ' . $model->children . '</span> / <span title="infant"><i class="fa fa-info"></i> ' . $model->infants . '</span>';
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
            'attribute' => 'cabin',
            'value' => static function (\common\models\Lead $model) {
                return $model->getCabinClassName();
            },
            'filter' => \common\models\Lead::CABIN_LIST
        ],
        [
            'attribute' => 'l_init_price',
            'value' => function (\common\models\Lead $model) {
                return $model->l_init_price ? number_format($model->l_init_price, 2) . ' $' : '-';
            },
            'contentOptions' => [
                'class' => 'text-right'
            ],
        ],
        [
            'class' => ActionColumn::class,
            'template' => '{take} <br> {view}',
            'visibleButtons' => [
                'view' => static function (Lead $model, $key, $index) {
                    return Auth::can('lead/view', ['lead' => $model]);
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
    'id' => 'lead-new-gv',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
]);
?>
<?php Pjax::end(); ?>
</div>
