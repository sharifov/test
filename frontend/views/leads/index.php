<?php

use frontend\widgets\multipleUpdate\button\MultipleUpdateButtonWidget;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use sales\auth\Auth;
use yii\helpers\Url;
use common\models\Email;
use common\models\Sms;
use common\models\Employee;
use sales\access\ListsAccess;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\Lead;
use yii\bootstrap\ActiveForm;
use yii\bootstrap4\Modal;
use yii\web\View;
use yii\helpers\ArrayHelper;
use common\models\LeadFlow;
use common\models\Call;

/* @var $this View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $isAgent bool */

$this->title = 'Search Leads';
$this->params['breadcrumbs'][] = $this->title;

$statusList = Lead::STATUS_LIST;

$user = Auth::user();

if ($user->isAdmin()) {
} else {
    if ($isAgent) {
        unset($statusList[Lead::STATUS_PENDING]);
    }
}

$lists = new ListsAccess($user->id);

$gridId = 'leads-grid-id';

?>
<style>
    .dropdown-menu {
        z-index: 1010;
    }
</style>
<div class="lead-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card multiple-update-summary" style="margin-bottom: 10px; display: none">
        <div class="card-header">
            <span class="pull-right clickable close-icon"><i class="fa fa-times"></i></span>
            Processing result log:
        </div>
        <div class="card-body"></div>
    </div>

<?php
$js = <<<JS
    $('.close-icon').on('click', function(){    
        $('.multiple-update-summary').slideUp();
    })
JS;
$this->registerJs($js);
?>


    <?php Pjax::begin(['id' => 'lead-pjax-list', 'timeout' => 7000, 'enablePushState' => true]); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]);?>

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

    <?php if (Auth::can('leads/index_MultipleUpdate')): ?>
        <?= MultipleUpdateButtonWidget::widget([
            'modalId' => 'modal-df',
            'showUrl' => Url::to(['/lead-multiple-update/show']),
            'gridId' => $gridId,
            'buttonClass' => 'multiple-update-btn',
            'buttonText' => 'Multiple update',
        ]) ?>
    <?php endif;?>

    <?php if (Auth::can('leads/index_Create_QA_Tasks')): ?>
        <?= MultipleUpdateButtonWidget::widget([
            'modalId' => 'modal-df',
            'showUrl' => Url::to(['/qa-task/qa-task-multiple/create-lead']),
            'gridId' => $gridId,
            'buttonClass' => 'qa-task-multiple-create',
            'buttonText' => 'Create QA Tasks',
            'headerText' => 'Create QA Tasks',
        ]) ?>
    <?php endif;?>

    <?php
    $gridColumns = [
        [
            'class' => '\kartik\grid\CheckboxColumn',
            'name' => 'LeadMultipleForm[lead_list]',
            'pageSummary' => true,
            'rowSelectedClass' => GridView::TYPE_INFO,
            'checkboxOptions' => function (Lead $model) {
                $can = Auth::can('leadSearchMultipleUpdate', ['lead' => $model]);
                return ['style' => 'display:' . ($can ? 'visible' : 'none')];
            },
            'visible' => Auth::can('leadSearchMultipleSelect')
        ],
        [
            'attribute' => 'id',
            'value' => static function (Lead $model) {
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
        'client_id:client',
        [
            'header' => 'Client / Emails / Phones',
            'format' => 'raw',
            'value' => static function (Lead $lead) use ($user) {
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

                if ($user->isAgent() && $lead->isOwner($user->id)) {
                    $str .= '- // - // - // -';
                } elseif ($lead->client) {
                    $str .= $lead->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', ArrayHelper::map($lead->client->clientEmails, 'email', 'email')) . '' : '';
                    $str .= $lead->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', ArrayHelper::map($lead->client->clientPhones, 'phone', 'phone')) . '' : '';
                }
                return $str ?? '-';
            },
            'options' => [
                'style' => 'width:180px'
            ]
        ],
        [
            'attribute' => 'status',
            'value' => static function (Lead $lead) {
                $statusValue = $lead->getStatusName(true);

                if ($lead->isTrash() && ($lastLeadFlow = $lead->lastLeadFlow)) {
                    if ($lastLeadFlow->status === $lead->status && $lastLeadFlow->lf_description) {
                        $statusValue .= ' <span data-toggle="tooltip" data-placement="top" title="' . Html::encode($lastLeadFlow->lf_description) . '"><i class="fa fa-warning"></i></span>';
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
            'value' => static function (Lead $model) {
                return Yii::$app->formatter->asRelativeTime(strtotime($model->created)); // Lead::getPendingAfterCreate($model->created);
            },
            'visible' => !$isAgent,
            'format' => 'raw'
        ],
        [
            'class' => \common\components\grid\project\ProjectColumn::class,
            'attribute' => 'project_id',
            'relation' => 'project',
            'onlyUserProjects' => true
        ],
        [
            'attribute' => 'cabin',
            'value' => static function (Lead $model) {
                return $model->getCabinClassName();
            },
            'filter' => Lead::CABIN_LIST
        ],
        [
            'label' => 'Pax / Communication',
            'value' => static function (Lead $model) {
                $str = '<i class="fa fa-male"></i> <span title="adult">' . $model->adults . '</span> / <span title="child">' . $model->children . '</span> / <span title="infant">' . $model->infants . '</span><br>';
                $str .= $model->getCommunicationInfo();
                return $str;
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],

        [
            'label' => 'PNR',
            'value' => static function (Lead $model) {
                $allPnr = $model->getAdditionalInformationMultiplePnr();
                if (!empty($allPnr) && isset($allPnr[0])) {
                    return '<code>' . implode('<br>', $allPnr) . '</code>';
                }
                return '-';
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],
        [
            'attribute' => 'hybrid_uid',
            'label' => '<span title="Hybrid UID">Booking ID</span>',
            'encodeLabel' => false,
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],
        [
            'header' => 'CheckList',
            'value' => static function (Lead $model) {
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
            'value' => static function (Lead $model) use ($isAgent) {
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
            'value' => static function (Lead $model) {
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
            'value' => static function (Lead $model) {
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
            'value' => static function (Lead $model) {
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
        [
            'attribute' => 'employee_id',
            'format' => 'raw',
            'value' => static function (Lead $model) {
                return $model->employee ? '<i class="fa fa-user"></i> ' . Html::encode($model->employee->username) : '-';
            },
            'filter' => $lists->getEmployees(true) ?: false
        ],
        [
            'attribute' => 'created',
            'value' => static function (Lead $model) {
                return $model->created ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created)) : '-';
            },
            'format' => 'raw',
            'filter' => false
        ],
        [
            'attribute' => 'updated',
            'value' => static function (Lead $model) {
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
            'value' => static function (Lead $model) {
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
    ];

    ?>

    <?php

    echo GridView::widget([
        'id' => $gridId,
        'dataProvider' => $dataProvider,
        'filterModel' => $isAgent ? false : $searchModel,

        'columns' => $gridColumns,
        'toolbar' => [
            [
                'content' =>
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', [
                        'leads/index'
                    ], [
                        'data-pjax' => 0,
                        'class' => 'btn btn-default',
                        'title' => 'Reset Grid'
                    ])

            ]
        ],
        'pjax' => false,
        /*'pjaxSettings' => [
            'options' => [
                'id' => 'lead-pjax-list2',
                'enablePushState' => true,
                'clientOptions' => ['method' => 'get']
            ],
        ],*/
        'striped' => true,
        'condensed' => true,
        'responsive' => false,
        'bsVersion' => '4.x',
        'hover' => true,
        'floatHeader' => false,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Leads</h3>'
        ]

    ]);
    ?>

    <br>
    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => true, 'class' => '', 'style' => 'overflow: hidden;']]); // ['action' => ['leads/update-multiple']?>

    <?= Html::button('<i class="fa fa-edit"></i> Multiple update', ['class' => 'btn btn-info multiple-update-btn']) ?>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end(); ?>

<?php

$js = <<<JS

    $(document).on('beforeSubmit', '#lead_form', function(event) {
        let btn = $(this).find('.search_leads_btn');
        
        btn.html('<span class="spinner-border spinner-border-sm"></span> Loading');        
        btn.prop("disabled", true)
    });

    $(document).on('pjax:end', function() {
         $('[data-toggle="tooltip"]').tooltip({html:true});
    });

   $('[data-toggle="tooltip"]').tooltip({html:true});

JS;
    $this->registerJs($js, View::POS_READY);
?>

