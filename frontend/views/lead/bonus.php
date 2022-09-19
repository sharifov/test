<?php

use common\components\grid\project\ProjectColumn;
use common\models\Employee;
use dosamigos\datepicker\DatePicker;
use src\access\ListsAccess;
use src\formatters\client\ClientTimeFormatter;
use src\helpers\lead\RemainingDayCalculator;
use src\model\client\helpers\ClientFormatter;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\Lead;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $isAgent bool */


$this->title = 'Bonus Queue';

$lists = new ListsAccess(Yii::$app->user->id);

$this->params['breadcrumbs'][] = $this->title;
/** @fflag FFlag::FF_KEY_HIDE_TASK_INFO_COLUMN_FROM_LEAD_SECTION_UI, Hide "Task info" column from Lead section on UI */
$isFFHideTaskInfoColumn = \Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_HIDE_TASK_INFO_COLUMN_FROM_LEAD_SECTION_UI);
?>

<style>
.dropdown-menu {
    z-index: 1010 !important;
}
</style>
<h1>
    <i class="fa fa-recycle"></i>
    <?=\yii\helpers\Html::encode($this->title)?>

</h1>
<div class="lead-bonus">

    <?php Pjax::begin(['timeout' => 5000, 'clientOptions' => ['method' => 'GET'], 'scrollTo' => 0]); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]);?>
    <?= $this->render('_search_bonus', ['model' => $searchModel]); ?>

    <?php
    $gridColumns = [
        [
            'attribute' => 'id',
            'label' => 'Lead ID',
            'value' => static function (Lead $lead) {
                return $lead->id;
            },
            'options' => [
                'style' => 'width:80px'
            ]
        ],
        [
            'class' => ProjectColumn::class,
            'attribute' => 'project_id',
            'relation' => 'project'
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
            'attribute' => 'created',
            'label' => 'Pending Time',
            'value' => static function (Lead $lead) {
                return Yii::$app->formatter->asRelativeTime(strtotime($lead->created)); //Lead::getPendingAfterCreate($model->created);
            },
            'format' => 'raw',
            'filter' => false
        ],
        [
            'attribute' => 'remainingDays',
            'label' => 'Remaining Days',
//            'value' => static function (Lead $lead) {
//                foreach ($lead->leadFlightSegments as $segment) {
//                    return RemainingDayCalculator::calculate($segment->airportByOrigin, $segment->departure);
//                }
//            },
            'format' => 'raw',
//            'filter' => false
        ],
        [
            'attribute' => 'created',
            'value' => static function (Lead $lead) {
                return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($lead->created));
            },
            'format' => 'raw',
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
        ],

        [
            // 'attribute' => 'client_id',
            'header' => 'Client',
            'format' => 'raw',
            'value' => static function (Lead $lead) {
                return $lead->getClientFormatted();
            },
            'options' => [
                'style' => 'width:160px'
            ]
        ],

        [
            'label' => 'Communication',
            'value' => static function (Lead $lead) {
                return $lead->getCommunicationInfo();
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],

        /*[
            'attribute' => 'clientTime',
            'label' => 'Client Time',
            'value' => static function ($model) {
                return Lead::getClientTime($model['id']);
            },
            'format' => 'raw'
        ],*/

       /*  [
            //'attribute' => 'client_id',
            'header' => 'Client time',
            'format' => 'raw',
            'value' => function(\common\models\Lead $model) {
                return ;
            },
            'options' => ['style' => 'width:100px'],
            //'filter' => \common\models\Employee::getList()
        ], */

        [
            'attribute' => 'Request Details',
            'value' => static function (Lead $lead) {
                return $lead->getFlightDetailsPaxFormatted();
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'Quotes',
            'value' => static function (Lead $model) {
                return $model->getQuoteInfoFormatted();
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],
        /*[
            'attribute' => 'last_activity',
            'label' => 'Last Activity',
            'value' => static function (\common\models\Lead $model) {
                return Lead::getLastActivity($model->getLastActivityByNote());
            },
            'format' => 'raw'
        ],*/

        /*[
            'attribute' => 'updated',
            'label' => 'Last Activity',
            'value' => static function (\common\models\Lead $model) {
                return '<span title="'.Yii::$app->formatter->asDatetime(strtotime($model->updated)).'">'.Yii::$app->formatter->asRelativeTime(strtotime($model->updated)).'</span>';
            },
            'format' => 'raw'
        ],*/

        [
            'attribute' => 'l_last_action_dt',
            //'label' => 'Last Update',
            'value' => static function (Lead $lead) {
                return $lead->l_last_action_dt ? '<b>' . Yii::$app->formatter->asRelativeTime(strtotime($lead->l_last_action_dt)) . '</b><br>' .
                    Yii::$app->formatter->asDatetime(strtotime($lead->l_last_action_dt)) : $lead->l_last_action_dt;
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'l_last_action_dt',
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
        ],

        [
            'attribute' => 'reason',
            'label' => 'Reason',
            'contentOptions' => [
                'style' => 'text-align:center;'
            ],
            'value' => static function (Lead $lead) {
                return '<span style="cursor:help;" class="label label-warning" title="' . Html::encode($lead->getLastReasonFromLeadFlow()) . '">&nbsp;<i class="fa fa-info-circle"></i>&nbsp;</span>';
            },
            'format' => 'raw'
        ],

        [
            'header' => 'Answ.',
            'attribute' => 'l_answered',
            'value' => static function (Lead $lead) {
                return $lead->l_answered ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'filter' => [1 => 'Yes', 0 => 'No'],
            'format' => 'raw'
        ],

        [
            'header' => 'Task Info',
            'value' => static function (Lead $lead) {
                return '<small style="font-size: 10px">' . Lead::getTaskInfo2($lead->id) . '</small>';
            },
            'format' => 'html',
            'contentOptions' => [
                'class' => 'text-left'
            ],
            'visible' => !$isAgent && !$isFFHideTaskInfoColumn,
            'options' => [
                'style' => 'width:140px'
            ]
        ],
        [
            'label' => 'Rating',
            'contentOptions' => [
                'style' => 'width: 90px;',
                'class' => 'text-center'
            ],
            'options' => [
                'class' => 'text-right'
            ],
            'value' => static function (Lead $lead) {
                return Lead::getRating2($lead->rating);
            },
            'format' => 'raw'
        ],


        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{action}',
            'buttons' => [
                'action' => static function ($url, Lead $lead, $key) {
                    $buttons = '';

                    $buttons .= Html::a('Take', [
                        'lead/take',
                        'gid' => $lead->gid
                    ], [
                        'class' => 'btn btn-primary btn-xs take-btn',
                        'data-pjax' => 0
                    ]);

                    $buttons .= Html::a('<i class="fa fa-search"></i>', ['lead/view', 'gid' => $lead->gid], [
                        'class' => 'btn btn-info btn-xs',
                        'target' => '_blank',
                        'data-pjax' => 0,
                        'title' => 'View lead'
                    ]);

                    return $buttons;
                }
            ]
        ]
    ];

    ?>
<?php
echo GridView::widget([
    'id' => 'lead-bonus-gv',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'toolbar' => false,
    'pjax' => false,
    'striped' => true,
    'condensed' => false,
    'responsive' => false,
    'hover' => true,
    'floatHeader' => false,
    'floatHeaderOptions' => [
        'scrollingTop' => 20
    ],
    /*'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Processing</h3>'
    ],*/

    'rowOptions' => static function (Lead $lead) {
        if ($lead->isProcessing() && $lead->isOwner(Yii::$app->user->id)) {
            return [
                'class' => 'highlighted'
            ];
        }

        /*if (in_array($model->status, [
            Lead::STATUS_ON_HOLD,
            Lead::STATUS_BOOKED,
            Lead::STATUS_FOLLOW_UP
        ])) {
            $now = new \DateTime();
            $departure = $model->getDeparture();

            $diff = ! empty($departure) ? $now->diff(new \DateTime($departure)) : $now->diff(new \DateTime($departure));
            $diffInSec = $diff->s + ($diff->i * 60) + ($diff->h * 3600) + ($diff->d * 86400) + ($diff->m * 30 * 86400) + ($diff->y * 12 * 30 * 86400);
            // if departure <= 7 days
            if ($diffInSec <= (7 * 24 * 60 * 60)) {
                return [
                    'class' => 'success'
                ];
            }
        }*/
    }

]);

?>

<?php Pjax::end(); ?>

</div>

<?php
$js = <<<JS
    $('.take-processing-btn').on('click', function (e) {
        e.preventDefault();
        let url = $(this).attr('href');
        if ($.inArray($(this).data('status'), [2, 8]) != -1) {
            let modal = $('#modal-df');
            $('#modal-df-label').html('Attention!');
            modal.find('.modal-body').html('');
            modal.find('.modal-body').load(url, function( response, status, xhr ) {
                if (status == 'error') {
                    alert(response);
                } else {
                    modal.modal('show');
                }
            });
        } else {
            window.location = url;
        }
    });

JS;
$this->registerJs($js);

/*$this->registerJsFile('/js/moment.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
$this->registerJsFile('/js/moment-timezone-with-data.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);*/

$this->registerJsFile('/js/jquery.countdown-2.2.0/jquery.countdown.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
