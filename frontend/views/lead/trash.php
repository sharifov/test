<?php

use dosamigos\datepicker\DatePicker;
use src\access\EmployeeProjectAccess;
use src\access\ListsAccess;
use src\formatters\client\ClientTimeFormatter;
use src\model\client\helpers\ClientFormatter;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use common\models\Lead;
use src\auth\Auth;

/**
 * @var common\models\Employee $user
 * @var $this yii\web\View
 * @var $searchModel common\models\search\LeadSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = 'Trash Queue';

$lists = new ListsAccess(Yii::$app->user->id);

$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .dropdown-menu {
        z-index: 1010 !important;
    }
</style>
<h1>
    <?= \yii\helpers\Html::encode($this->title) ?>
</h1>
<div class="lead-index">

    <?php Pjax::begin(['timeout' => 6000, 'scrollTo' => 0]); ?>

    <?php $form = ActiveForm::begin([
            'action' => ['trash'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>

        <div class="row">
            <div class="col-md-3">
                <?php
                echo  \kartik\daterange\DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date_range',
                    'useWithAddon' => true,
                    'presetDropdown' => true,
                    'hideInput' => true,
                    'convertFormat' => true,
                    'startAttribute' => 'datetime_start',
                    'endAttribute' => 'datetime_end',
                    'pluginOptions' => [
                        'timePicker' => true,
                        'timePickerIncrement' => 1,
                        'timePicker24Hour' => true,
                        'locale' => [
                            'format' => 'Y-m-d H:i',
                            'separator' => ' - '
                        ],
                        'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
                    ]
                ]);
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <?= $form->field($searchModel, 'is_conversion')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => '-']) ?>
            </div>

            <div class="col-md-1" style="padding-top: 25px; margin-left: 15px;">
                <?= Html::submitButton('<i class="fa fa-search"></i> Show result', ['class' => 'btn btn-success']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

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
            'attribute' => 'created',
            'label' => 'Pending Time',
            'value' => static function (\common\models\Lead $model) {
                $str = Yii::$app->formatter->asRelativeTime(strtotime($model->created));
                $str .= $model->created ? '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created), 'php: Y-m-d [H:i:s]')  : '-';
                return $str;
            },
            'options' => [
                'style' => 'width:160px'
            ],
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
                    'autocomplete' => 'off'
                ],
                'clientEvents' => [
                    'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                ]
            ]),
        ],

        [
            'attribute' => 'updated',
            'label' => 'Trash Date',
            'value' => static function (\common\models\Lead $model) {
                $str = Yii::$app->formatter->asRelativeTime(strtotime($model->updated));
                $str .= $model->updated ? '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated), 'php: Y-m-d [H:i:s]')  : '-';
                return $str;
            },
            'options' => [
                'style' => 'width:160px'
            ],
            'format' => 'raw',
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'updated',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'clearBtn' => true
                ],
                'options' => [
                    'autocomplete' => 'off'
                ],
                'clientEvents' => [
                    'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                ]
            ]),
        ],

        /*[
            'attribute' => 'created',
            'value' => static function (\common\models\Lead $model) {
                return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
            },
            'format' => 'raw',
            'filter' => false

        ],*/

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

                    /*$str = $model->client && $model->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')) . '' : '';
                    $str .= $model->client && $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) . '' : '';

                    $clientName .= '<br>' . $str;*/
                } else {
                    $clientName = '-';
                }

                $communicationInfo = '<br /><br />';
                $communicationInfo .= $model->getCommunicationInfo();

                return $clientName . $communicationInfo;
            },
            'options' => [
                'style' => 'width:220px'
            ],
            'visible' => !$user->isQa()
        ],/*
        [
            'attribute' => 'clientTime',
            'label' => 'Client Time',
            'value' => static function ($model) {
                return Lead::getClientTime($model['id']);
            },
            'format' => 'raw'
        ], */

        [
            'header' => 'Client time',
            'format' => 'raw',
            'value' => static function (\common\models\Lead $model) {
                return ClientTimeFormatter::format($model->getClientTime2(), $model->offset_gmt);
            },
            'options' => [
                'style' => 'width:110px'
            ],
            'visible' => !$user->isQa()
        ],

        [
            'attribute' => 'Request Details',
            'content' => function (\common\models\Lead $model) {
                $content = '';
                $content .= $model->getFlightDetails();
                $content .= ' (<i class="fa fa-male"></i> x' . ($model->adults . '/' . $model->children . '/' . $model->infants) . ')<br/>';

                $content .= sprintf('<strong>Cabin:</strong> %s', $model->getCabinClassName());

                return $content;
            },
            'format' => 'raw',
            'visible' => !$user->isQa()
        ],
//        [
//            'attribute' => 'Quotes ',
//            'value' => static function (\common\models\Lead $model) {
//                $quotes = $model->getQuoteSendInfo();
//                return sprintf('Total: <strong>%d</strong> <br> Sent: <strong>%d</strong>', ($quotes['send_q'] + $quotes['not_send_q']), $quotes['send_q']);
//            },
//            'format' => 'raw'
//        ],

        [
            'attribute' => 'Quotes',
            'value' => static function (\common\models\Lead $model) {
                $cnt = $model->quotesCount;
                return $cnt ?: '-';
            },
            'options' => [
                'style' => 'width:60px',
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ],
            //'format' => 'raw'
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
            'label' => 'SMS',
            'value' => static function (\common\models\Lead $model) {
                $cnt = $model->getCountSms();
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
            'label' => 'Emails',
            'value' => static function (\common\models\Lead $model) {
                $cnt = $model->getCountEmails();
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

        /*[
            //'header' => 'Agent',
            'attribute' => 'employee_id',
            'format' => 'raw',
            'value' => static function (\common\models\Lead $model) {
                return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
            },
            'filter' => $lists->getEmployees(),
        ],*/

        [
            'class' => \common\components\grid\UserSelect2Column::class,
            'attribute' => 'employee_id',
            'relation' => 'employee',
            'placeholder' => 'Select User',
        ],

        /*[
            'attribute' => 'update',
            'label' => 'Last Update',
            'value' => static function (\common\models\Lead $model) {
                return '<span title="' . Yii::$app->formatter->asDatetime(strtotime($model->updated)) . '">' . Yii::$app->formatter->asRelativeTime(strtotime($model->updated)) . '</span>';
            },
            'format' => 'raw'
        ],*/
        [
            'label' => 'Reason',
            'contentOptions' => [
                'style' => 'max-width: 250px;'
            ],
            'value' => static function (\common\models\Lead $model) {
                return '<pre>' . $model->getLastReasonFromLeadFlow()  . '</pre>';
            },
            'format' => 'raw'
        ],
        /*[
            'label' => 'Rating',
            'contentOptions' => [
                'style' => 'width: 90px;',
                'class' => 'text-center'
            ],
            'options' => [
                'class' => 'text-right'
            ],
            'value' => static function (\common\models\Lead $model) {
                return Lead::getRating2($model->rating);
            },
            'format' => 'raw'
        ],*/

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{take} <br> {view}',
            'visibleButtons' => [
                'take' => static function (Lead $model, $key, $index) {
                    return Auth::user()->isAgent();
                },
                /*'view' => static function (Lead $model, $key, $index) {
                    return Auth::can('lead/view', ['lead' => $model]);
                },*/
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
                        'title' => 'View lead',
                    ]);
                }
            ],
        ]

        /*[
            'class' => 'yii\grid\ActionColumn',
            'template' => '{action}',
            'buttons' => [
                'action' => function ($url, \common\models\Lead $model, $key) {
                    $buttons = '';

                    $buttons .= Html::a('<i class="fa fa-search"></i> view', [
                        'lead/view',
                        'gid' => $model->gid
                    ], [
                        'class' => 'btn btn-info btn-xs',
                        'target' => '_blank',
                        'data-pjax' => 0,
                        'title' => 'View lead'
                    ]);

                    return $buttons;
                }
            ]
        ]*/
    ];

    ?>
    <?php

    echo \yii\grid\GridView::widget([
        'id' => 'lead-trash-gv',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
        'rowOptions' => function (Lead $model) {
            if ($model->status === Lead::STATUS_PROCESSING && Yii::$app->user->id == $model->employee_id) {
                return [
                    'class' => 'highlighted'
                ];
            }
        }
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
