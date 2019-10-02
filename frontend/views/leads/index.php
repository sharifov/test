<?php

use yii\helpers\Url;
use common\models\Email;
use common\models\Sms;
use common\models\Employee;
use sales\ui\user\ListsAccess;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\Lead;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\ArrayHelper;
use common\models\Reason;
use common\models\LeadFlow;
use common\models\Call;

/* @var $this View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $multipleForm frontend\models\LeadMultipleForm */
/* @var $isAgent bool */
/** @var array $report */

$this->title = 'Search Leads';
$this->params['breadcrumbs'][] = $this->title;

$statusList = Lead::STATUS_LIST;

/** @var Employee $user */
$user = Yii::$app->user->identity;

if ($user->isAdmin()) {

} else {
    if ($isAgent) {
        unset($statusList[Lead::STATUS_PENDING]);
    }
}

$lists = new ListsAccess($user->id);

?>
<style>
    .dropdown-menu {
        z-index: 1010;
    }
</style>
<div class="lead-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php Pjax::begin(['id' => 'lead-pjax-list', 'timeout' => 7000, 'enablePushState' => true]); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>

    <?php if ($report): ?>
        <div class="error-summary">
            <ul>
            <?php foreach ($report as $item): ?>
                <li><?= $item ?></li>
            <?php endforeach;?>
            </ul>
        </div>
    <?php endif;?>

    <?php

    if ($isAgent) {
        $searchTpl = '_search_agent';
    } else {
        $searchTpl = '_search';
    }

    echo $this->render($searchTpl, [
        'model' => $searchModel,
        'action' => 'index',
        'lists' => $lists
    ]);
    ?>

    <?php if ($user->isAdmin() || $user->isSupervision()) : ?>
        <p>
            <? //= Html::a('Create Lead', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::button('<i class="fa fa-edit"></i> Multiple update', ['class' => 'btn btn-info', 'data-toggle' => "modal", 'data-target' => "#modalUpdate"]) ?>
        </p>
    <?php endif; ?>

    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => true]]); // ['action' => ['leads/update-multiple'] ?>

    <?php

    $gridColumns = [
        // ['class' => 'yii\grid\SerialColumn'],

        [
            'class' => '\kartik\grid\CheckboxColumn',
            'name' => 'LeadMultipleForm[lead_list]',
            'pageSummary' => true,
            'rowSelectedClass' => GridView::TYPE_INFO,
            'checkboxOptions' => function (Lead $model) {
                $can = Yii::$app->user->can('leadSearchMultipleUpdate', ['lead' => $model]);
                return ['style' => 'display:' . ($can ? 'visible' : 'none')];
            },
            'visible' => Yii::$app->user->can('leadSearchMultipleSelect')
        ],

        /*[
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'LeadMultipleForm[lead_list]'
                'checkboxOptions' => function(Lead $model) {
                    return ['value' => $model->id];
                },
        ],*/

        /*[

            'header'=>Html::checkbox('selection_all', false, ['class'=>'select-on-check-all', 'value'=>1,
                'onclick'=>'
                    $(".kv-row-checkbox").prop("checked", $(this).is(":checked"));
                    if($(".kv-row-checkbox").prop("checked") === true) $(".delete_ready").attr("class","delete_ready warning");
                    if($(".kv-row-checkbox").prop("checked") === false) $(".delete_ready").attr("class","delete_ready");


                    ']),
            'contentOptions'=>['class'=>'kv-row-select'],
            'content'=>function($model, $key){


                    return Html::checkbox('id[]', false, ['class'=>'kv-row-checkbox ',
                        'value'=>$key, 'onclick'=>'$(this).closest("tr").toggleClass("warning");']);

                //return Html::checkbox('selection[]', false, ['class'=>'kv-row-checkbox', 'value'=>$key, 'onclick'=>'$(this).closest("tr").toggleClass("danger");', 'disabled'=> isset($model->stopDelete)&&!($model->stopDelete===1)]);
            },
            'hAlign'=>'center',
            'vAlign'=>'middle',
            'hiddenFromExport'=>true,
            'mergeHeader'=>true,
            'width'=>'50px'
        ],*/

        [
            'attribute' => 'id',
            'value' => function (Lead $model) {
                return Html::a('<i class="fa fa-file-o"></i> ' . $model->id, [
                    'lead/view', 'gid' => $model->gid
                ], [
                    'data-pjax' => 0,
                    'target' => '_blank'
                ]);
            },
            'format' => 'raw',
            'options' => [
                'style' => 'width:80px'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],

        [
            'attribute' => 'uid',
            'options' => [
                'style' => 'width:100px'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],

        [
            'attribute' => 'client_id',
            'value' => function (Lead $model) {
                return $model->client_id ? Html::a($model->client_id, ['client/index', 'ClientSearch[id]' => $model->client_id], ['data-pjax' => 0, 'target' => '_blank']) : '-';
            },
            'format' => 'raw',
            'options' => [
                'style' => 'width:80px'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],
        /*[
            // 'attribute' => 'client_id',
            'header' => 'Client name',
            'format' => 'raw',
            'value' => function (Lead $model) {

                if ($model->client) {
                    $clientName = $model->client->first_name . ' ' . $model->client->last_name;
                    if ($clientName === 'Client Name') {
                        $clientName = '- - - ';
                    } else {
                        $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                    }
                } else {
                    $clientName = '-';
                }

                return $clientName;
            },
            'options' => [
                'style' => 'width:160px'
            ]
            // 'filter' => \common\models\Employee::getList()
        ],*/

        [
            'header' => 'Client / Emails / Phones',
            'format' => 'raw',
            'value' => function (Lead $lead) use ($user) {

                if ($lead->client) {
                    $clientName = $lead->client->first_name . ' ' . $lead->client->last_name;
                    if ($clientName === 'Client Name') {
                        $clientName = '- - - ';
                    } else {
                        $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                    }
                } else {
                    $clientName = '-';
                }

                $str = $clientName . '<br>';

                if ($user->isAgent() && $lead->isOwner($user->id, false)) {
                    $str .= '- // - // - // -';
                } elseif ($lead->client) {
                    $str .= $lead->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', ArrayHelper::map($lead->client->clientEmails, 'email', 'email')) . '' : '';
                    $str .= $lead->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', ArrayHelper::map($lead->client->clientPhones, 'phone', 'phone')) . '' : '';
                }

                /*$str .= '<br>';
                $str .= '<span title="Calls Out / In"><i class="fa fa-phone"></i> '. $lead->getCountCalls(Call::CALL_TYPE_OUT) .'/'.  $lead->getCountCalls(Call::CALL_TYPE_IN) .'</span> | ';
                $str .= '<span title="SMS Out / In"><i class="fa fa-comments"></i> '. $lead->getCountSms(Sms::TYPE_OUTBOX) .'/'.  $lead->getCountCalls(Sms::TYPE_INBOX) .'</span> | ';
                $str .= '<span title="Email Out / In"><i class="fa fa-envelope"></i> '. $lead->getCountEmails(Email::TYPE_OUTBOX) .'/'.  $lead->getCountEmails(Email::TYPE_INBOX) .'</span>';*/

                //$strArr[] = $str;

                //$str = implode('<br>', $strArr);

                return $str ?? '-';
            },
            'options' => [
                'style' => 'width:180px'
            ]
        ],

        /*[
            'header' => 'Client Phones',
            'value' => function(Lead $model) {
                return $model->client && $model->client->clientPhones ? implode(', ', ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) : '-';
            },
        ],*/

        //'employee_id',
        //'status',
        [
            'attribute' => 'status',
            'value' => function (Lead $lead) {
                $statusValue = $lead->getStatusName(true);

                if ($lead->isTrash()) {
                    $reason = Reason::find()->where([
                        'lead_id' => $lead->id
                    ])
                        ->orderBy([
                            'id' => SORT_DESC
                        ])
                        ->one();
                    if ($reason) {
                        $statusValue .= ' <span data-toggle="tooltip" data-placement="top" title="' . Html::encode($reason->reason) . '"><i class="fa fa-warning"></i></span>';
                    }
                }

                $statusLog = LeadFlow::find()->where([
                    'lead_id' => $lead->id,
                    'status' => $lead->status
                ])
                    ->orderBy([
                        'id' => SORT_DESC
                    ])
                    ->one();

                if ($statusLog) {
                    // $statusValue .= '<br><span class="label label-default">'.Yii::$app->formatter->asDatetime(strtotime($statusLog->created)).'</span>';
                    $statusValue .= '<br><br><span class="label label-default">' . Yii::$app->formatter->asDatetime(strtotime($statusLog->created)) . '</span>';
                    $statusValue .= '<br>' . Yii::$app->formatter->asRelativeTime(strtotime($statusLog->created)) . '';
                }

                return $statusValue;
            },
            'format' => 'raw',
            'filter' => Lead::STATUS_LIST,
            'options' => [
                'style' => 'width:100px'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ]

        ],
        [
            'attribute' => 'created',
            'label' => 'Pending Time',
            'value' => function (Lead $model) {
                return Yii::$app->formatter->asRelativeTime(strtotime($model->created)); // Lead::getPendingAfterCreate($model->created);
            },
            'visible' => !$isAgent,
            'format' => 'raw'
        ],
        [
            'attribute' => 'project_id',
            'value' => function (Lead $model) {
                return $model->project ? $model->project->name : '-';
            },
            'filter' => $lists->getProjects(),
            'label' => 'Project'
        ],

        // 'project_id',
        // 'source_id',
        /*[
            'attribute' => 'source_id',
            'value' => function (Lead $model) {
                return $model->source ? $model->source->name : '-';
            },
            'filter' => \common\models\Source::getList(),
            'visible' => ! $isAgent
        ],

        [
            'attribute' => 'trip_type',
            'value' => function (Lead $model) {
                return Lead::getFlightType($model->trip_type) ?? '-';
            },
            'filter' => Lead::TRIP_TYPE_LIST
        ],*/

        [
            'attribute' => 'cabin',
            'value' => function (Lead $model) {
                return $model->getCabinClassName();
            },
            'filter' => Lead::CABIN_LIST
        ],

        // 'trip_type',
        // 'cabin',
        // 'adults',

        /*[
            'label' => 'Pax',
            'value' => function (Lead $model) {
                return '<i class="fa fa-male"></i> <span title="adult">'. $model->adults .'</span> / <span title="child">' . $model->children . '</span> / <span title="infant">' . $model->infants.'</span>';
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],*/

        [
            'label' => 'Pax / Communication',
            'value' => function (Lead $model) {
                //$str = '';
                $str = '<i class="fa fa-male"></i> <span title="adult">' . $model->adults . '</span> / <span title="child">' . $model->children . '</span> / <span title="infant">' . $model->infants . '</span><br>';
                $str .= '<span title="Calls Out / In"><i class="fa fa-phone success"></i> ' . $model->getCountCalls(Call::CALL_TYPE_OUT) . '/' . $model->getCountCalls(Call::CALL_TYPE_IN) . '</span> | ';
                $str .= '<span title="SMS Out / In"><i class="fa fa-comments info"></i> ' . $model->getCountSms(Sms::TYPE_OUTBOX) . '/' . $model->getCountCalls(Sms::TYPE_INBOX) . '</span> | ';
                $str .= '<span title="Email Out / In"><i class="fa fa-envelope danger"></i> ' . $model->getCountEmails(Email::TYPE_OUTBOX) . '/' . $model->getCountEmails(Email::TYPE_INBOX) . '</span>';
                return $str;
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],

        /*[
            'attribute' => 'adults',
            'value' => function (Lead $model) {
                return $model->adults ?: 0;
            },
            'filter' => array_combine(range(0, 9), range(0, 9)),
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],

        [
            'attribute' => 'children',
            'value' => function (Lead $model) {
                return $model->children ?: '-';
            },
            'filter' => array_combine(range(0, 9), range(0, 9)),
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],*/

        /*[
            'attribute' => 'infants',
            'value' => function(Lead $model) {
                return $model->infants ?: '-';
            },
            'filter' => array_combine(range(0, 9), range(0, 9)),
            'contentOptions' => ['class' => 'text-center'],
        ],*/

        [
            // 'header' => 'Grade',
            'attribute' => 'l_grade',
            'value' => function (Lead $model) {
                return $model->l_grade;
            },
            'filter' => array_combine(range(0, 9), range(0, 9)),
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'visible' => !$isAgent
        ],

        [
            // 'header' => 'Grade',
            'attribute' => 'l_answered',
            'value' => function (Lead $model) {
                return $model->l_answered ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
            },
            'filter' => [
                1 => 'Yes',
                0 => 'No'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'format' => 'raw'

            // 'visible' => !$isAgent
        ],

        /*[
            'header' => 'Task Info',
            'value' => function (Lead $model) use ($isAgent) {
                return '<small style="font-size: 10px">' . $model->getTaskInfo() . '</small>';
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-left'
            ],
            'visible' => ! $isAgent,
            'options' => [
                'style' => 'width:200px'
            ]
        ],*/

        [
            'header' => 'CheckList',
            'value' => function (Lead $model) {
                return '<small style="font-size: 10px">' . $model->getCheckListInfo() . '</small>';
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-left'
            ],
            'visible' => !$isAgent,
            'options' => [
                'style' => 'width:200px'
            ]
        ],

        [
            'header' => 'Quotes',
            'value' => function (Lead $model) use ($isAgent) {
                if ($model->quotesCount) {
                    if ($isAgent) {
                        return $model->quotesCount;
                    }
                    return Html::a($model->quotesCount, [
                        'quotes/index',
                        'QuoteSearch[lead_id]' => $model->id
                    ], [
                        'target' => '_blank',
                        'data-pjax' => 0
                    ]);
                }
                return '-';
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],
        [
            'header' => 'Expert Quotes',
            'value' => function (Lead $model) {
                return $model->quotesExpertCount ? Html::a($model->quotesExpertCount, [
                    'quotes/index',
                    "QuoteSearch[lead_id]" => $model->id
                ], [
                    'target' => '_blank',
                    'data-pjax' => 0
                ]) : '-';
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'visible' => !$isAgent,
        ],
        [
            'header' => 'Segments',
            'value' => function (Lead $model) {
                $segmentData = [];
                foreach ($model->leadFlightSegments as $sk => $segment) {
                    $segmentData[] = ($sk + 1) . '. <code>' . Html::a($segment->origin . ' <i class="fa fa-long-arrow-right"></i> ' . $segment->destination, [
                            'lead-flight-segment/view',
                            'id' => $segment->id
                        ], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]) . '</code>';
                }
                $segmentStr = implode('<br>', $segmentData);
                return '' . $segmentStr . '';
                // return $model->leadFlightSegmentsCount ? Html::a($model->leadFlightSegmentsCount, ['lead-flight-segment/index', "LeadFlightSegmentSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
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
            'header' => 'Depart',
            'value' => function (Lead $model) {
                foreach ($model->leadFlightSegments as $sk => $segment) {
                    return date('d-M-Y', strtotime($segment->departure));
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
        // 'children',
        // 'infants',
        // 'notes_for_experts:ntext',

        // 'updated',
        // 'request_ip',
        // 'request_ip_detail:ntext',

        [
            'attribute' => 'employee_id',
            'format' => 'raw',
            'value' => function (Lead $model) {
                return $model->employee ? '<i class="fa fa-user"></i> ' . Html::encode($model->employee->username) : '-';
            },
            'filter' => $lists->getEmployees() ?: false
        ],

        // 'rating',
        // 'called_expert',
        /*
         * [
         * 'attribute' => 'discount_id',
         * 'options' => ['style' => 'width:100px'],
         * 'contentOptions' => ['class' => 'text-center'],
         * ],
         */
        // 'offset_gmt',
        // 'snooze_for',
        // 'created',
        [
            'attribute' => 'created',
            'value' => function (Lead $model) {
                return $model->created ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created)) : '-';
            },
            'format' => 'raw',
            'filter' => false
        ],
        // 'created:date',

        [
            'attribute' => 'updated',
            'value' => function (Lead $model) {
                $str = '-';
                if ($model->updated) {
                    $str = '<b>' . Yii::$app->formatter->asRelativeTime(strtotime($model->updated)) . '</b>';
                    $str .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated));
                }
                return $str;
            },
            'format' => 'raw',
            'filter' => false,
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],

        [
            'attribute' => 'l_last_action_dt',
            'value' => function (Lead $model) {
                $str = '-';
                if ($model->l_last_action_dt) {
                    $str = '<b>' . Yii::$app->formatter->asRelativeTime(strtotime($model->l_last_action_dt)) . '</b>';
                    $str .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->l_last_action_dt));
                }
                return $str;
            },
            'format' => 'raw',
            'filter' => false,
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],

        // 'bo_flight_id',

        /*[
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}'
        ]*/
    ];

    ?>



    <?php

    /*
     * if(Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id)) {
     *
     *
     * echo \yii\grid\GridView::widget([
     * 'dataProvider' => $dataProvider,
     * //'filterModel' => Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id) ? false : $searchModel,
     * 'columns' => $gridColumns,
     *
     * ]);
     * } else {
     */

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $isAgent ? false : $searchModel,
        // 'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false

        /*
         * 'export' => [
         * 'label' => 'Page',
         * 'fontAwesome' => true,
         * 'itemsAfter'=> [
         * '<li role="presentation" class="divider"></li>',
         * '<li class="dropdown-header">Export All Data</li>',
         * $fullExportMenu
         * ]
         * ],
         */

        'columns' => $gridColumns,

        'toolbar' => [
            [
                'content' =>
                // Html::button('<i class="glyphicon glyphicon-plus"></i>', ['type'=>'button', 'title'=>'Add Lead', 'class'=>'btn btn-success', 'onclick'=>'alert("This will launch the book creation form.\n\nDisabled for this demo!");']) . ' '.
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', [
                        'leads/index'
                    ], [
                        'data-pjax' => 0,
                        'class' => 'btn btn-default',
                        'title' => 'Reset Grid'
                    ])

            ]
            // '{export}',
            // $fullExportMenu,
            // '{toggleData}'
        ],
        'pjax' => false,
        /*'pjaxSettings' => [
            'options' => [
                'id' => 'lead-pjax-list2',
                'enablePushState' => true,
                'clientOptions' => ['method' => 'get']
            ],
        ],*/

        //'bordered' => true,
        'striped' => false,
        'condensed' => false,
        'responsive' => true,
        'hover' => true,
        'floatHeader' => true,
        'floatHeaderOptions' => [
            'scrollingTop' => 20
        ],
        /*'showPageSummary' => true,*/
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Leads</h3>'
        ]

    ]);
    // }

    ?>


    <?php if ($user->isAdmin() || $user->isSupervision()) : ?>
        <p>
            <?= Html::button('<i class="fa fa-edit"></i> Multiple update', ['class' => 'btn btn-info', 'data-toggle' => "modal", 'data-target' => "#modalUpdate"]) ?>
        </p>

        <?php

        Modal::begin([
            'header' => '<b>Multiple update selected Leads</b>',
            // 'toggleButton' => ['label' => 'click me'],
            'id' => 'modalUpdate'
            // 'size' => 'modal-lg',
        ]);
        ?>

        <?= $form->errorSummary($multipleForm) ?>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?php

                        if ($user->isAdmin()) {
                            $role = 'admin';
                        } elseif ($user->isSupervision()) {
                            $role = 'supervision';
                        } else {
                            $role = null;
                        }

                        ?>
                        <?= $form->field($multipleForm, 'status_id')->dropDownList(Lead::getStatusList($role), ['prompt' => '-', 'id' => 'status_id']) ?>

                        <div id="reason_id_div" style="display: none">
                            <?= $form->field($multipleForm, 'reason_id')->dropDownList(Reason::getReasonListByStatus(Lead::STATUS_PROCESSING), ['prompt' => '-', 'id' => 'reason_id']) // Lead::STATUS_REASON_LIST  ?>

                            <div id="reason_description_div"
                                 style="display: none">
                                <?= $form->field($multipleForm, 'reason_description')->textarea(['rows' => '3']) ?>
                            </div>
                        </div>

                        <?php
                            if ($employees = $lists->getEmployees()) {
                                $employees[-1] = '--- REMOVE EMPLOYEE ---';
                                echo $form->field($multipleForm, 'employee_id')->dropDownList($employees, ['prompt' => '-']);
                            }
                        ?>

                        <div class="form-group text-right">
                            <?= Html::submitButton('<i class="fa fa-check-square"></i> Update selected Leads', ['class' => 'btn btn-info']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php Modal::end(); ?>
    <?php endif; ?>

    <?php ActiveForm::end(); ?>



    <?php Pjax::end(); ?>


    <?php
    $ajaxUrl = Url::to([
        "leads/ajax-reason-list"
    ]);
    $js = <<<JS

    $(document).on('pjax:start', function() {
        $("#modalUpdate .close").click();
    });

    $(document).on('pjax:end', function() {
         $('[data-toggle="tooltip"]').tooltip();
    });

    $(document).on('change', '#reason_id', function() {
        if( $(this).val() == '0' ) {
            $('#reason_description_div').show();
        }  else {
            $('#reason_description_div').hide();
        }
    });

     $(document).on('change', '#status_id', function() {
         var status_id = $(this).val();
        if( status_id > 0 ) {
            $('#reason_id_div').show();

           $.post("$ajaxUrl",{status_id: status_id}, function( data ) {
                $("#reason_id").html( data ).trigger('change');
           })

        }  else {
            $('#reason_id_div').hide();
        }
    });


   $('[data-toggle="tooltip"]').tooltip();


JS;
    $this->registerJs($js, View::POS_READY);
    ?>


</div>
