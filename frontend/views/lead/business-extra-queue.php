<?php

use common\components\grid\project\ProjectColumn;
use modules\lead\src\abac\queue\LeadBusinessExtraQueueAbacObject;
use yii\grid\ActionColumn;
use src\auth\Auth;
use src\formatters\client\ClientTimeFormatter;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
//use yii\grid\GridView;
use common\models\Lead;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $isAgent bool */

$this->title = 'Business Extra Queue';
$this->params['breadcrumbs'][] = $this->title;
?>

    <h1>
        <i class="fa fa-history"></i> <?=\yii\helpers\Html::encode($this->title)?>
    </h1>

    <div class="lead-extra-queue">

        <?php Pjax::begin(['timeout' => 5000, 'clientOptions' => ['method' => 'GET'], 'scrollTo' => 0]); ?>
        <?= $this->render('_search_business_extra_queue', ['model' => $searchModel]); ?>

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
                    return Yii::$app->formatter->asRelativeTime(strtotime($lead->created));
                },
                'format' => 'raw',
                'filter' => false
            ],
            [
                'attribute' => 'remainingDays',
                'label' => 'Remaining Days',
                'format' => 'raw',
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
            ],
            [
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
            [
                'attribute' => 'l_last_action_dt',
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
                'visible' => ! $isAgent,
                'options' => [
                    'style' => 'width:140px'
                ]
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{take} <br> {view}',
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
                'visibleButtons' => [
                    'take' => static function ($model, $key, $index) {
                        /** @abac LeadBusinessExtraQueueAbacObject::UI_ACCESS, LeadBusinessExtraQueueAbacObject::ACTION_TAKE, Access to button take */
                        return \Yii::$app->abac->can(
                            null,
                            LeadBusinessExtraQueueAbacObject::UI_ACCESS,
                            LeadBusinessExtraQueueAbacObject::ACTION_TAKE
                        );
                    },
                ],
            ]
        ];

        ?>
        <?php
        echo GridView::widget([
            'id' => 'lead-extra-queue-gv',
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

            'rowOptions' => static function (Lead $lead) {
                if ($lead->isOwner(Yii::$app->user->id)) {
                    return [
                        'class' => 'highlighted'
                    ];
                }
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

$this->registerJsFile('/js/jquery.countdown-2.2.0/jquery.countdown.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
