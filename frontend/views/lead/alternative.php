<?php

use yii\grid\SerialColumn;
use common\models\Employee;
use dosamigos\datepicker\DatePicker;
use src\formatters\client\ClientTimeFormatter;
use src\model\client\helpers\ClientFormatter;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\Lead;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $checkShiftTime bool */
/* @var $isAccessNewLead bool */
/* @var $accessLeadByFrequency array */
/* @var $user Employee */

$this->title = 'Leads Alternative';

$this->registerJsFile('/js/moment.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
$this->registerJsFile('/js/jquery.countdown-2.2.0/jquery.countdown.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);

$this->params['breadcrumbs'][] = $this->title;
?>

    <h1>
        <i class="fa fa-briefcase"></i> <?= \yii\helpers\Html::encode($this->title) ?>
    </h1>

    <div class="lead-failed-bookings">

        <?php Pjax::begin(['scrollTo' => 0]); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>

        <div class="row">
            <?php if (!$checkShiftTime) : ?>
                <div class="col-md-12">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <strong>Warning!</strong> New leads are only available on your shift. (Current You time: <?= Yii::$app->formatter->asTime(time()) ?>)
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!$isAccessNewLead) : ?>
                <div class="col-md-12">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <strong>Warning!</strong> Access is denied - action "take new lead"
                    </div>
                </div>
            <?php endif; ?>


            <?php if (!empty($accessLeadByFrequency) && $accessLeadByFrequency['access'] == false) : ?>
                <div class="col-md-12">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <strong>Warning!</strong> New leads will be available in <span id="left-time-countdown"
                                                                                       data-elapsed="<?= $accessLeadByFrequency['takeDtUTC']->format('U') - time() ?>"
                                                                                       data-countdown="<?= $accessLeadByFrequency['takeDtUTC']->format('Y-m-d H:i') ?>"><?= Yii::$app->formatter->asTime($accessLeadByFrequency['takeDt']) ?></span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php

        $gridColumns = [
            ['class' => SerialColumn::class],
            [
                'attribute' => 'id',
                'label' => 'Lead ID',
                'value' => static function (\common\models\Lead $model) {
                    return $model->id;
                },
                'visible' => !$user->isAgent(),
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
                //'attribute' => 'pending',
                'label' => 'Pending Time',
                'value' => static function (\common\models\Lead $model) {
                    return Yii::$app->formatter->asRelativeDt($model->created);
                },
                'options' => [
                    'style' => 'width:180px'
                ],
                'format' => 'raw',
                'visible' => !$user->isAgent(),
            ],
            [
                'attribute' => 'expiration_dt',
                'value' => static function (Lead $model) {
                    return Yii::$app->formatter->asExpirationDt($model->l_expiration_dt);
                },
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'expiration_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'clearBtn' => true,
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date',
                        'readonly' => '1',
                    ],
                    'clientEvents' => [
                        'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                    ],
                ]),
                'options' => [
                    'style' => 'width:180px'
                ],
                'format' => 'raw',
            ],
            [
                'attribute' => 'created',
                'value' => static function (\common\models\Lead $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                },

                'format' => 'raw',
                'options' => [
                    'style' => 'width:180px'
                ],
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'clearBtn' => true
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date'
                    ],
                    'clientEvents' => [
                        'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                    ]
                ]),
                'enableSorting' => !$user->isAgent(),
            ],

            [
                // 'attribute' => 'client_id',
                'header' => 'Client',
                'format' => 'raw',
                'value' => static function (\common\models\Lead $model) {

                    if ($model->client) {
                        $clientName = $model->client->first_name . ' ' . $model->client->last_name;
                        if ($clientName === 'Client Name') {
                            $clientName = '- - - ';
                        } else {
                            $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                        }

                        if ($model->client->isExcluded()) {
                            $clientName = ClientFormatter::formatExclude($model->client)  . $clientName;
                        }
                        $str = '';
                        //$str = $model->client && $model->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')) . '' : '';
                        //$str .= $model->client && $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) . '' : '';

                        $clientName .= '<br>' . $str;
                    } else {
                        $clientName = '-';
                    }

                    return $clientName;
                },
                'visible' => !$user->isAgent(),
                'options' => [
                    'style' => 'width:160px'
                ]
            ],/*
        [
            'attribute' => 'clientTime',
            'label' => 'Client Time',
            'value' => static function ($model) {
                return Lead::getClientTime($model['id']);
            },
            'format' => 'raw'
        ], */


            /*[
                'attribute' => 'Request Details',
                'content' => function (\common\models\Lead $model) {
                    $content = '';
                    $content .= $model->getFlightDetails();
                    $content .= ' (<i class="fa fa-male"></i> x' . ($model->adults . '/' . $model->children . '/' . $model->infants) . ')<br/>';

                    $content .= sprintf('<strong>Cabin:</strong> %s', Lead::getCabin($model['cabin']));

                    return $content;
                },
                'format' => 'raw'
            ],*/

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
                'visible' => !$user->isAgent(),
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
                'visible' => !$user->isAgent(),
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

            /*[
                'header' => 'Client time',
                'format' => 'raw',
                'value' => static function (\common\models\Lead $model) {
                    return $model->getClientTime();
                },
                'visible' => !$user->isAgent(),
                //'options' => ['style' => 'width:110px'],

            ],*/

            [
                'header' => 'Client time',
                'format' => 'raw',
                'value' => function (\common\models\Lead $model) {
                    return ClientTimeFormatter::format($model->getClientTime2(), $model->offset_gmt);
                },
                //'options' => ['style' => 'width:80px'],
                //'filter' => \common\models\Employee::getList()
            ],

            [
                'header' => 'Project',
                'attribute' => 'project_id',
                'filter' => false,
                'value' => static function (\common\models\Lead $model) {
                    return $model->project ? $model->project->name : '-';
                },
            ],

            /*[
                'header' => 'Client time2',
                'format' => 'raw',
                'value' => static function (\common\models\Lead $model) {
                    return $model->getClientTime2();
                },
                'options' => [
                    'style' => 'width:110px'
                ]
            ],*/

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{action}',
                'buttons' => [
                    'action' => static function ($url, \common\models\Lead $model, $key) use ($checkShiftTime, $isAccessNewLead, $user) {
                        $buttons = '';

                        if (!$isAccessNewLead) {
                            $buttons .= '<i class="fa fa-warning warning"></i> Access is denied (limit) - "Take lead"<br/>';
                        }

                        if (!$checkShiftTime) {
                            $buttons .= '<i class="fa fa-warning warning"></i> Time shift limit access<br>';
                        }

                        if (!$buttons) {
                            $buttons .= Html::a('<i class="fa fa-download"></i> Take', ['lead/take', 'gid' => $model->gid], [
                                'class' => 'btn btn-primary btn-xs take-btn',
                                'data-pjax' => 0
                            ]);

                            if (!$user->isAgent()) {
                                $buttons .= Html::a('<i class="fa fa-search"></i> View', ['lead/view', 'gid' => $model->gid], [
                                    'class' => 'btn btn-info btn-xs',
                                    'data-pjax' => 0
                                ]);
                            }
                        }

                        return $buttons;
                    }
                ]
            ]
        ];

        ?>
        <?php
        echo '<div class="table-responsive">' . GridView::widget([
                'id' => 'lead-alternative-gv',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => $gridColumns,
                'toolbar' => false,
                'pjax' => false,
                'striped' => true,
                'condensed' => false,
                'responsive' => false,
                'hover' => true,
                'floatHeaderOptions' => [
                    'scrollingTop' => 20
                ],

            ]) . '</div>';
        ?>
        <?php Pjax::end(); ?>
    </div>

<?php
$js = '
function initCountDown()
{
    $("[data-countdown]").each(function() {
      var $this = $(this), finalDate = $(this).data("countdown");
      var elapsedTime = $(this).data("elapsed");

        var seconds = new Date().getTime() + (elapsedTime * 1000);
        $this.countdown(seconds, function(event) {
            $(this).html(event.strftime(\'%H:%M:%S\'));
        });
    });
}

$(document).on(\'pjax:end\', function() {
    initCountDown();
    setClienTime();
});

initCountDown();

';

$this->registerJs($js);