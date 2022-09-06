<?php

use common\components\grid\Select2Column;
use frontend\widgets\multipleUpdate\button\MultipleUpdateButtonWidget;
use kartik\select2\Select2;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use src\auth\Auth;
use src\model\client\helpers\ClientFormatter;
use yii\helpers\Url;
use common\models\Email;
use common\models\Sms;
use common\models\Employee;
use src\access\ListsAccess;
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
/* @var $accessAdvancedSearch bool */

$this->title = 'Search Leads';
$this->params['breadcrumbs'][] = $this->title;

$statusList = Lead::STATUS_LIST;

$user = Auth::user();

if ($user->isAdmin()) {
} else {
    if (!$accessAdvancedSearch) {
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


    <?php Pjax::begin(['id' => 'lead-pjax-list', 'timeout' => 7000, 'enablePushState' => true, 'scrollTo' => 0]); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]);?>

    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Search</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: <?=(Yii::$app->request->isPjax || Yii::$app->request->get('LeadSearch')) ? 'block' : 'none'?>">
            <?php
            if (!$accessAdvancedSearch) {
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
        </div>
    </div>

    <?php if (Auth::can('leads/index_MultipleUpdate')) : ?>
        <?= MultipleUpdateButtonWidget::widget([
            'modalId' => 'modal-df',
            'showUrl' => Url::to(['/lead-multiple-update/show']),
            'gridId' => $gridId,
            'buttonClass' => 'multiple-update-btn',
            'buttonClassAdditional' => 'btn btn-info btn-warning',
            'buttonText' => 'Multiple update',
        ]) ?>
    <?php endif;?>

    <?php if (Auth::can('leads/index_Create_QA_Tasks')) : ?>
        <?= MultipleUpdateButtonWidget::widget([
            'modalId' => 'modal-df',
            'showUrl' => Url::to(['/qa-task/qa-task-multiple/create-lead']),
            'gridId' => $gridId,
            'buttonClass' => 'qa-task-multiple-create',
            'buttonClassAdditional' => 'btn btn-info btn-default',
            'buttonText' => 'Create QA Tasks',
            'headerText' => 'Create QA Tasks',
            'faIconClass' => 'fa-plus-square',
        ]) ?>
    <?php endif;?>

    <?php

    $showFilter = $accessAdvancedSearch;

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
                return Html::a($model->id, [
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
            ],
            'filter' => $showFilter,
        ],
        [
            'attribute' => 'uid',
            'options' => [
                'style' => 'width:100px'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'filter' => $showFilter,
        ],
        [
            'attribute' => 'l_type',
            'value' => static function (Lead $model) {
                return $model->l_type ? '<span class="label label-default" style="font-size: 13px">' . $model::TYPE_LIST[$model->l_type] . '</span>' : ' - ';
            },
            'format' => 'raw',
            'visible' => $searchModel->show_fields && in_array('l_type', $searchModel->show_fields, true),
            'filter' => $showFilter ? Lead::TYPE_LIST : false,
        ],
        [
            'class' => \common\components\grid\project\ProjectColumn::class,
            'attribute' => 'project_id',
            'relation' => 'project',
            'onlyUserProjects' => $showFilter,
            'filter' => $showFilter ? null : false,
        ],
        [
            'class' => \common\components\grid\department\DepartmentColumn::class,
            'label' => 'Department',
            'attribute' => 'l_dep_id',
            'relation' => 'lDep',
        ],
        [
            'attribute' => 'client_id',
            'value' => static function (Lead $model) {
                return Yii::$app->formatter->asClient($model->client_id);
            },
            'format' => 'raw',
            'filter' => $showFilter,
        ],
        [
            //'header' => 'Client / Emails / Phones',
            'header' => 'Client',
            'format' => 'raw',
            'value' => static function (Lead $lead) use ($user) {
                if ($lead->client) {
                    $clientName = $lead->client->first_name . ' ' . $lead->client->last_name;
                    if ($clientName === 'Client Name') {
                        $clientName = '- - - ';
                    } else {
                        $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                    }
                    if ($lead->client->isExcluded()) {
                        $clientName = ClientFormatter::formatExclude($lead->client)  . $clientName;
                    }
                } else {
                    $clientName = '-';
                }

//                $str = $clientName . '<br>';
//
//                if ($user->isAgent() && $lead->isOwner($user->id)) {
//                    $str .= '- // - // - // -';
//                } elseif ($lead->client) {
//                    $str .= $lead->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', ArrayHelper::map($lead->client->clientEmails, 'email', 'email')) . '' : '';
//                    $str .= $lead->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', ArrayHelper::map($lead->client->clientPhones, 'phone', 'phone')) . '' : '';
//                }
                return $clientName; // ?? '-';
            },
            'options' => [
                'style' => 'width:180px'
            ],
        ],
        [
            'attribute' => 'status',
            'value' => static function (Lead $lead) {
                $statusValue = $lead->getStatusName(true);
                $statusChangeInfo = '';
                if ($lead->hasTakenFromBonusToProcessing()) {
                    $statusChangeInfo = Html::tag('span', '<i class="fa fa-star"></i>', [
                        'title' => 'Lead has been taken from Bonus Queue',
                        'style' => 'font-size:larger; margin-left:10px',
                    ]);
                } elseif ($lead->hasTakenFromExtraToProcessing()) {
                    $statusChangeInfo = Html::tag('span', '<i class="fa fa-star-o"></i>', [
                        'title' => 'Lead has been taken from Extra Queue',
                        'style' => 'font-size:larger; margin-left:10px',
                    ]);
                }

                if ($lead->isTrash() && ($lastLeadFlow = $lead->lastLeadFlow)) {
                    if ($lastLeadFlow->status === $lead->status && $lastLeadFlow->lf_description) {
                        $statusValue .= ' <span data-toggle="tooltip" data-placement="top" title="' . Html::encode($lastLeadFlow->lf_description) . '"><i class="fa fa-warning"></i></span>';
                    }
                }
                return $statusValue . $statusChangeInfo;
            },
            'format' => 'raw',
            'filter' => $showFilter ? Lead::STATUS_LIST : false,
            'options' => [
                'style' => 'width:100px'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],

        [
            'attribute' => 'l_status_dt',
            'value' => static function (Lead $model) {
                return $model->l_status_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->l_status_dt)) : '-';
            },
            'format' => 'raw',
            'filter' => false
        ],

        [
            'attribute' => 'status_flow',
            'value' => static function (Lead $lead) {
                $statusValue = '';
                $statusLog = LeadFlow::find()->where([
                    'lead_id' => $lead->id,
                    'status' => $lead->status
                ])
                    ->orderBy([
                        'id' => SORT_DESC
                    ])
                    ->one();

                if ($statusLog) {
                    $statusValue .= '<span class="label label-default">' . Yii::$app->formatter->asDatetime(strtotime($statusLog->created)) . '</span>';
                    $statusValue .= '<br>' . Yii::$app->formatter->asRelativeTime(strtotime($statusLog->created)) . '';
                }

                return $statusValue;
            },
            'format' => 'raw',
            'options' => [
                'style' => 'width:100px'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'visible' => $searchModel->show_fields && in_array('status_flow', $searchModel->show_fields, true),
        ],

        [
            'attribute' => 'created',
            'label' => 'Pending Time',
            'value' => static function (Lead $model) {
                return Yii::$app->formatter->asRelativeTime(strtotime($model->created)); // Lead::getPendingAfterCreate($model->created);
            },
            'visible' => $accessAdvancedSearch,
            'format' => 'raw'
        ],
        [
            'attribute' => 'l_expiration_dt',
            'value' => static function (Lead $model) {
                return Yii::$app->formatter->asExpirationDt($model->l_expiration_dt);
            },
            'format' => 'raw',
            'visible' => $searchModel->show_fields && in_array('expiration_dt', $searchModel->show_fields, true),
        ],
        [
            'attribute' => 'cabin',
            'value' => static function (Lead $model) {
                return $model->getCabinClassName();
            },
            'filter' => $showFilter ? Lead::CABIN_LIST : false,
        ],
        [
            'label' => 'Pax',
            'value' => static function (Lead $model) {
                $str = '<i class="fa fa-male"></i> <span title="adult">' . $model->adults . '</span> / <span title="child">' . $model->children . '</span> / <span title="infant">' . $model->infants . '</span>';
                return $str;
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],
        [
            'label' => 'Files',
            'attribute' => 'count_files',
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'visible' => $searchModel->show_fields && in_array('count_files', $searchModel->show_fields, true),
        ],

        [
            'label' => 'Communication',
            'value' => static function (Lead $model) {
                $str = $model->getCommunicationInfo();
                return $str;
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'visible' => $searchModel->show_fields && in_array('communication', $searchModel->show_fields, true),
        ],

        /*[
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
            ],
            'visible' => $searchModel->show_fields && in_array('pnr', $searchModel->show_fields, true),
        ],*/
        [
            'attribute' => 'hybrid_uid',
            'label' => '<span title="Hybrid UID">Booking ID</span>',
            'encodeLabel' => false,
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'visible' => $searchModel->show_fields && in_array('hybrid_uid', $searchModel->show_fields, true),
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
            'options' => [
                'style' => 'width:200px'
            ],
            'visible' => ($accessAdvancedSearch && $searchModel->show_fields && in_array('check_list', $searchModel->show_fields, true)),
        ],
        [
            'header' => 'Quotes',
            'value' => static function (Lead $model) use ($accessAdvancedSearch) {
                if ($model->quotesCount) {
                    if (!$accessAdvancedSearch) {
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
            ],
            'visible' => $searchModel->show_fields && in_array('quotes', $searchModel->show_fields, true),
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
            'visible' => ($accessAdvancedSearch && $searchModel->show_fields && in_array('expert_quotes', $searchModel->show_fields, true)),
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
            ],
            'visible' => $searchModel->show_fields && in_array('segments', $searchModel->show_fields, true),
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
            ],
            'visible' => $searchModel->show_fields && in_array('depart', $searchModel->show_fields, true),
        ],
        [
            'class' => Select2Column::class,
            'attribute' => 'employee_id',
            'format' => 'raw',
            'value' => static function (Lead $model) {
                return $model->employee ? '<i class="fa fa-user"></i> ' . Html::encode($model->employee->username) : '-';
            },
            'data' => $lists->getEmployees(true) ?: [],
            'filter' => $accessAdvancedSearch ? true : '',
            'id' => 'employee-filter',
            'options' => ['width' => '200px'],
            'pluginOptions' => ['allowClear' => true]
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
            'visible' => $searchModel->show_fields &&  in_array('updated', $searchModel->show_fields, true),
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
            'visible' => $searchModel->show_fields && in_array('l_last_action_dt', $searchModel->show_fields, true),
            'format' => 'raw',
            'filter' => false,
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],
    ];

    ?>


    <?php /*= $form->field($model, 'timezone')->widget(\kartik\select2\Select2::class, [
        'data' => \common\models\Employee::timezoneList(true),
        'size' => \kartik\select2\Select2::SMALL,
        'options' => ['placeholder' => 'Select TimeZone', 'multiple' => false],
        'pluginOptions' => ['allowClear' => true],
    ]);*/ ?>



    <?php
    echo GridView::widget([
        'id' => $gridId,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,

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
    //        'panel' => [
    //            'type' => GridView::TYPE_PRIMARY,
    //            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Leads</h3>'
    //        ]

    ]);
    ?>

    <br>
    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => true, 'class' => '', 'style' => 'overflow: hidden;']]); // ['action' => ['leads/update-multiple']?>

    <?= Html::button('<i class="fa fa-edit"></i> Multiple update', ['class' => 'btn btn-warning multiple-update-btn']) ?>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end(); ?>

<?php

$js = <<<JS

    // $('#mySelect2').on('select2:select', function (e) {
    //     var data = e.params.data;
    //     console.log(data);
    // });
    //
    // $(document).on('select2:select', '#showFields', function(e) {
    //     /*alert(123);
    //      var data = e.params.data;
    //     console.log(data);*/
    // });

   
    
    //  $(document).on('change', '#showFields', function(e) {
    //     alert(123);
    //     var data = e.params.data;
    //     console.log(data);
    // });



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

