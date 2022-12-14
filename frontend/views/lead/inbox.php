<?php

use dosamigos\datepicker\DatePicker;
use src\formatters\client\ClientTimeFormatter;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
//use yii\grid\GridView;
use common\models\Lead;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $checkShiftTime bool */
/* @var $isAgent bool */
/* @var $isAccessNewLead bool */
/* @var $accessLeadByFrequency array */
/* @var $user \common\models\Employee */
/* @var $newLeadsCount integer */
/* @var $taskSummary array */

$this->title = 'Inbox Queue';

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jQuery-Knob/1.2.13/jquery.knob.min.js', [
    //'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
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
    <i class="fa fa-briefcase"></i> <?=\yii\helpers\Html::encode($this->title)?>
</h1>

<div class="lead-index">

    <div class="col-md-12">

        <div class="animated flipInY col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-check-square-o"></i>
                </div>
                <div class="count"><?=$taskSummary['completedTasksCount']?></div>

                <h3>Completed tasks</h3>
                <p>Current shift</p>
            </div>
        </div>

        <div class="animated flipInY col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-list"></i>
                </div>
                <div class="count"><?=$taskSummary['allTasksCount']?></div>

                <h3>All tasks</h3>
                <p>Current shift</p>
            </div>
        </div>

        <?php /*
        <div class="col-md-3">
            <table class="table table-bordered">
                <tr>
                    <th>Completed tasks / All tasks</th>
                    <td><?=$taskSummary['completedTasksCount']?> / <?=$taskSummary['allTasksCount']?></td>
                </tr>
                <tr>
                    <th>Current Shift task progress</th>
                    <td style="width: 50%">
                        <div class="progress" title="<?=$taskSummary['completedTasksPercent']?>%">
                            <div class="progress-bar" role="progressbar" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100" style="width: <?=$taskSummary['completedTasksPercent']?>%;">
                                <?=$taskSummary['completedTasksPercent']?>%
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>*/ ?>

        <div class="col-md-1" title="Completed Tasks Percent">
            <input type="text" value="<?=$taskSummary['completedTasksPercent']?>" data-width="120" data-height="120" data-fgColor="<?=($taskSummary['completedTasksPercent'] >= $user->userParams->up_min_percent_for_take_leads ? '#66CC66' : '#f3a72d')?>" class="knob_element" readonly="readonly" title="Completed Tasks Percent">
        </div>

        <div class="col-md-1" title="Taked leads">
            <?php
            if ($user->userParams->up_default_take_limit_leads && $newLeadsCount > $user->userParams->up_default_take_limit_leads) {
                $maxVal = $isAccessNewLead ? $newLeadsCount + 1 : $newLeadsCount;
            } else {
                $maxVal = $user->userParams->up_default_take_limit_leads;
            }
            ?>
            <input type="text" value="<?=$newLeadsCount?>" data-max="<?=$maxVal?>" data-width="120" data-height="120" data-fgColor="#337ab7" class="knob_element" readonly="readonly" title="Taked leads">
        </div>

        <?= $this->render('@frontend/widgets/views/user_info_progress', [
                'user' => $user,
                'completedTasksPercent' => $taskSummary['completedTasksPercent'],
                'newLeadsCount' => $newLeadsCount
        ])?>

    </div>

    <div class="clearfix"></div>

    <?php Pjax::begin(['scrollTo' => 0]); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>


    <div class="row">
    <?php if (!$checkShiftTime) : ?>
        <div class="col-md-4">
                <div class="alert alert-warning alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Warning!</strong> New leads are only available on your shift. (Current You time: <?=Yii::$app->formatter->asTime(time())?>)
                </div>

                <?php /*php \yii\helpers\VarDumper::dump(Yii::$app->user->identity->getShiftTime(), 10, true)?>
                <?php echo date('Y-m-d H:i:s')*/?>
        </div>
    <?php endif; ?>

    <?php if (!$isAccessNewLead) : ?>
        <div class="col-md-4">
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Warning!</strong> Access is denied - action "take new lead"
            </div>
        </div>
    <?php endif; ?>


    <?php if (!empty($accessLeadByFrequency) && $accessLeadByFrequency['access'] == false) : ?>
        <div class="col-md-4">
            <div class="alert alert-warning alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Warning!</strong> New leads will be available in <span id="left-time-countdown" data-elapsed="<?= $accessLeadByFrequency['takeDtUTC']->format('U') - time()?>" data-countdown="<?= $accessLeadByFrequency['takeDtUTC']->format('Y-m-d H:i')?>"><?=Yii::$app->formatter->asTime($accessLeadByFrequency['takeDt'])?></span>
                 </div>
        </div>
    <?php endif; ?>
    </div>

    <?php

    $gridColumns = [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'id',
            'label' => 'Lead ID',
            'value' => static function (Lead $model) {
                return $model->id;
            },
            'visible' => ! $isAgent,
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
            'value' => static function (Lead $model) {
                $createdTS = strtotime($model->created);

                $diffTime = time() - $createdTS;
                $diffHours = (int) ($diffTime / (60 * 60));

                return ($diffHours > 3 && $diffHours < 73 ) ? $diffHours . ' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
            },
            'options' => [
                'style' => 'width:180px'
            ],
            'format' => 'raw',
            'visible' => ! $isAgent,
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
                ],
                'options' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'Choose Date'
                ],
            ]),
            'enableSorting' => ! $isAgent
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

                    /*$str = $model->client && $model->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')) . '' : '';
                    $str .= $model->client && $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) . '' : '';

                    $clientName .= '<br>' . $str;*/
                } else {
                    $clientName = '-';
                }

                return $clientName;
            },
            'visible' => ! $isAgent,
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
            'visible' => ! $isAgent,
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
            'visible' => ! $isAgent,
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
            'visible' => ! $isAgent,
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
                'action' => function ($url, \common\models\Lead $model, $key) use ($checkShiftTime, $isAccessNewLead, $isAgent) {
                    $buttons = '';

                    if ($isAgent) {
                        if (!$isAccessNewLead) {
                            $buttons .= '<i class="fa fa-warning warning"></i> Access is denied (limit) - "Take lead"<br/>';
                        }

                        if (!$checkShiftTime) {
                            $buttons .= '<i class="fa fa-warning warning"></i> Time shift limit access<br>';
                        }
                    }


                    if (!$buttons) {
                        $buttons .= Html::a('<i class="fa fa-download"></i> Take', ['lead/take', 'gid' => $model->gid], [
                            'class' => 'btn btn-primary btn-xs take-btn',
                            'data-pjax' => 0
                        ]);

                        if (!$isAgent) {
                            $buttons .= ' &nbsp';
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
    'id' => 'lead-inbox-gv',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'toolbar' => false,
    'pjax' => false,
    'striped' => true,
    'condensed' => false,
    'responsive' => false,
    'hover' => true,
//    'floatHeader' => true,
    'floatHeaderOptions' => [
        'scrollingTop' => 20
    ],
    /*'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Processing</h3>'
    ],*/

    'rowOptions' => function (Lead $model) {
        if ($model->status === Lead::STATUS_PROCESSING && Yii::$app->user->id == $model->employee_id) {
            return [
                'class' => 'highlighted'
            ];
        }

        /*
         * if (in_array($model->status, [
         * Lead::STATUS_ON_HOLD,
         * Lead::STATUS_BOOKED,
         * Lead::STATUS_FOLLOW_UP
         * ])) {
         * $now = new \DateTime();
         * $departure = $model->getDeparture();
         *
         * $diff = ! empty($departure) ? $now->diff(new \DateTime($departure)) : $now->diff(new \DateTime($departure));
         * $diffInSec = $diff->s + ($diff->i * 60) + ($diff->h * 3600) + ($diff->d * 86400) + ($diff->m * 30 * 86400) + ($diff->y * 12 * 30 * 86400);
         * // if departure <= 7 days
         * if ($diffInSec <= (7 * 24 * 60 * 60)) {
         * return [
         * 'class' => 'success'
         * ];
         * }
         * }
         */
    }

]) . '</div>';
?>
<?php Pjax::end(); ?>
</div>

<?php //if($isAccessNewLead):?>
    <?php $this->registerJs('$(".knob_element").knob();', \yii\web\View::POS_READY); ?>
<?php //endif; ?>

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