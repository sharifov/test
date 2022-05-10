<?php

use src\auth\Auth;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\bootstrap4\Modal;

/* @var $this yii\web\View */
/* @var $leadModel \common\models\Lead */
/* @var $caseModel \src\entities\cases\Cases */
/* @var $model \common\models\Lead */
/* @var $isAdmin boolean */

/**
* @var $leadSearchModel common\models\search\LeadSearch
* @var $leadDataProvider yii\data\ArrayDataProvider
*/

$isAgent = false;

?>

<div class="x_panel">
    <?php Pjax::begin(['id' => 'pjax-lead-info', 'timeout' => 7000, 'enablePushState' => true]); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]);?>

    <div class="x_title">
        <h2><i class="fa fa-cube"></i> Lead Info</h2>
        <ul class="nav navbar-right panel_toolbox">

            <?php if (true) : ?>
                <?php if (!$leadModel && (int) $caseModel->cs_dep_id === \common\models\Department::DEPARTMENT_EXCHANGE) : ?>
                <li>
                    <?= \yii\bootstrap\Html::a('<i class="fa fa-plus-circle success"></i> Create Lead', ['lead/create-case', 'case_gid' => $caseModel->cs_gid], ['data-pjax' => 0, 'target' => '_blank'])?>
                </li>
                <?php endif; ?>
                <?php if ($caseModel->isProcessing()) :?>
                <li>
                    <?=Html::a('<i class="fa fa-search warning"></i> Search Lead', null, ['class' => 'modal', 'id' => 'search-lead-btn', 'title' => 'Search Lead for Case'])?>
                </li>
                <?php endif; ?>
                <?php /*<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                    <ul class="dropdown-menu" role="menu">
                        <li> <?= Html::a('<i class="fa fa-remove"></i> Decline Quotes', null, [
                                //'class' => 'btn btn-primary btn-sm',
                                'id' => 'btn-declined-quotes',
                            ]) ?>
                        </li>
                    </ul>
                </li>*/?>
            <?php endif; ?>
            <li>
                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block;">
        <?php if ($leadModel) :?>
        <div class="row">
            <div class="col-md-6">
                <?= \yii\widgets\DetailView::widget([
                    'model' => $leadModel,
                    'attributes' => [
                        'id',
                        [
                            'label' => 'Client Name',
                            'format' => 'raw',
                            'value' => static function (\common\models\Lead $model) {
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
                                $str = $clientName;
                                return $str ?? '-';
                            },
                        ],

                        [
                            'label' => 'Client Emails / Phones',
                            'format' => 'raw',
                            'value' => static function (\common\models\Lead $model) {
                                $clientEmails = $model->client ? $model->client->getEmailList() : [];
                                foreach ($clientEmails as $key => $element) {
                                    $clientEmails[$key] = \src\helpers\email\MaskEmailHelper::masking($element);
                                }

                                $clientPhones = \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone');

                                foreach ($clientPhones as $key => $element) {
                                    $clientPhones[$key] = \src\helpers\phone\MaskPhoneHelper::masking($element);
                                }

                                $str = \frontend\widgets\SliceAndShowMoreWidget::widget([
                                    'data' => $model->client->getOnlyEmails() ?? [],
                                    'separator' => '<i class="fa fa-envelope"></i> '
                                ]);

                                $str .= \frontend\widgets\SliceAndShowMoreWidget::widget([
                                    'data' => $model->client->getOnlyPhones() ?? [],
                                    'separator' => '<i class="fa fa-phone"></i> '
                                ]);
                                return $str ?? '-';
                            },
                        ],

                        [
                            'attribute' => 'status',
                            'value' => static function (\common\models\Lead $model) {
                                $statusValue = $model->getStatusName(true);
                                return $statusValue;
                            },
                            'format' => 'raw',


                        ],

                        [
                            'attribute' => 'status',
                            'value' => static function (\common\models\Lead $model) {
                                $statusValue = '';

                                $statusLog = \common\models\LeadFlow::find()->where([
                                    'lead_id' => $model->id,
                                    'status' => $model->status
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


                        ],
                        [
                            //'attribute' => 'created',
                            'label' => 'Pending Time',
                            'value' => static function (\common\models\Lead $model) {
                                return Yii::$app->formatter->asRelativeTime(strtotime($model->created)); // Lead::getPendingAfterCreate($model->created);
                            },
                            //'format' => 'raw'
                        ],
                        [
                            'attribute' => 'created',
                            'value' => function (\common\models\Lead $model) {
                                return $model->created ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created)) : '';
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'updated',
                            'value' => function (\common\models\Lead $model) {
                                return $model->updated ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated)) : '';
                            },
                            'format' => 'raw',
                        ],

                    ],
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= \yii\widgets\DetailView::widget([
                    'model' => $leadModel,
                    'attributes' => [
                        [
                            'attribute' => 'project_id',
                            'value' => static function (\common\models\Lead $model) {
                                return $model->project ? $model->project->name : '-';
                            },
                        ],

                        [
                            'attribute' => 'cabin',
                            'value' => static function (\common\models\Lead $model) {
                                return $model->getCabinClassName();
                            },
                        ],

                        //'trip_type',

                        [
                            'label' => 'Pax',
                            'value' => static function (\common\models\Lead $model) {
                                //$str = '';
                                $str = '<i class="fa fa-male"></i> <span title="adult">' . $model->adults . '</span> / <span title="child">' . $model->children . '</span> / <span title="infant">' . $model->infants . '</span>';
                                return $str;
                            },
                            'format' => 'raw',
                        ],

                        [
                            'label' => 'Communication',
                            'value' => static function (\common\models\Lead $model) {
                                return $model->getCommunicationInfo();
                            },
                            'format' => 'raw',
                        ],

                        [
                            'label' => 'Quotes',
                            'value' => static function (\common\models\Lead $model) use ($isAgent) {
                                return $model->quotesCount;
                            },
                            'format' => 'raw',
                        ],

                        [
                            'label' => 'Segments',
                            'value' => static function (\common\models\Lead $model) {
                                $segments = $model->leadFlightSegments;
                                $segmentData = [];
                                if ($segments) {
                                    foreach ($segments as $sk => $segment) {
                                        $segmentData[] = ($sk + 1) . '. <code>' . ($segment->origin . ' <i class="fa fa-long-arrow-right"></i> ' . $segment->destination) . '</code>';
                                    }
                                }

                                $segmentStr = implode('<br>', $segmentData);
                                return '' . $segmentStr . '';
                            },
                            'format' => 'raw',
                        ],

                        [
                            'label' => 'Depart',
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
                        ],

                    ],
                ]) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php Pjax::end(); ?>
</div>


    <style type="text/css">
        @media screen and (min-width: 768px) {
            .modal-dialog {
                width: 700px; /* New width for default modal */
            }
            .modal-sm {
                width: 350px; /* New width for small modal */
            }
        }
        @media screen and (min-width: 992px) {
            .modal-lg {
                width: 80%; /* New width for large modal */
            }
        }
        .grid-view pre {
            max-width: 1000px;
        }
    </style>


<?php
Modal::begin([
    'id' => 'modalLeadSearch',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);
?>

<?php Pjax::begin(['id' => 'lead-pjax-list', 'timeout' => 7000, 'enablePushState' => true]); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]);?>
<?php
echo $this->render('_search_lead_form', [
    'caseModel' => $caseModel,
    'model' => $leadSearchModel,
]);
?>

<?php
    $gridColumns = [
        // ['class' => 'yii\grid\SerialColumn'],

            [
            'attribute' => 'id',
            'value' => static function (\common\models\Lead $model) {
                return $model->id; /*Html::a('<i class="fa fa-file-o"></i> ' . $model->id, [
                    'lead/view', 'gid' => $model->gid
                ], [
                    'data-pjax' => 0,
                    'target' => '_blank'
                ]);*/
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

        /*[
            'attribute' => 'client_id',
            'value' => static function (\common\models\Lead $model) {
                return $model->client_id ? Html::a($model->client_id, ['client/index', 'ClientSearch[id]' => $model->client_id], ['data-pjax' => 0, 'target' => '_blank']) : '-';
            },
            'format' => 'raw',
            'options' => [
                'style' => 'width:80px'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],*/

            [
            'header' => 'Client / Emails / Phones',
            'format' => 'raw',
            'value' => static function (\common\models\Lead $model) {
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

                $str = $clientName . '<br>';

                if (Auth::id() !== $model->employee_id && Auth::user()->isAgent()) {
                    $str .= '- // - // - // -';
                } else {
                    $str .= \frontend\widgets\SliceAndShowMoreWidget::widget([
                        'data' => $model->client->getOnlyEmails() ?? [],
                        'separator' => '<i class="fa fa-envelope"></i> '
                    ]);

                    $str .= \frontend\widgets\SliceAndShowMoreWidget::widget([
                        'data' => $model->client->getOnlyPhones() ?? [],
                        'separator' => '<i class="fa fa-phone"></i> '
                    ]);
                }

                return $str ?? '-';
            },
            'options' => [
                'style' => 'width:180px'
            ]
            ],

            [
            'attribute' => 'status',
            'value' => static function (\common\models\Lead $model) {
                $statusValue = $model->getStatusName(true);

                if ($model->isTrash() && ($lastLeadFlow = $model->lastLeadFlow)) {
                    if ($lastLeadFlow->status === $model->status && $lastLeadFlow->lf_description) {
                        $statusValue .= ' <span data-toggle="tooltip" data-placement="top" title="' . Html::encode($lastLeadFlow->lf_description) . '"><i class="fa fa-warning"></i></span>';
                    }
                }

                $statusLog = \common\models\LeadFlow::find()->where([
                    'lead_id' => $model->id,
                    'status' => $model->status
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
            'filter' => \common\models\Lead::STATUS_LIST,
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
            'value' => static function (\common\models\Lead $model) {
                return Yii::$app->formatter->asRelativeTime(strtotime($model->created)); // Lead::getPendingAfterCreate($model->created);
            },
            'visible' => !$isAgent,
            'format' => 'raw'
            ],
            [
            'attribute' => 'project_id',
            'value' => static function (\common\models\Lead $model) {
                return $model->project ? $model->project->name : '-';
            },
            'filter' => \common\models\Project::getList()
            ],

            [
            'attribute' => 'cabin',
            'value' => static function (\common\models\Lead $model) {
                return $model->getCabinClassName();
            },
            'filter' => \common\models\Lead::CABIN_LIST
            ],

        //'trip_type',

            [
            'label' => 'Pax / Communication',
            'value' => static function (\common\models\Lead $model) {
                //$str = '';
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
            'header' => 'Quotes',
            'value' => static function (\common\models\Lead $model) use ($isAgent) {
                return $model->quotesCount;
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ]
            ],

            [
            'header' => 'Segments',
            'value' => static function (\common\models\Lead $model) {
                $segments = $model->leadFlightSegments;
                $segmentData = [];
                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        $segmentData[] = ($sk + 1) . '. <code>' . ($segment->origin . ' <i class="fa fa-long-arrow-right"></i> ' . $segment->destination) . '</code>';
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
            'attribute' => 'employee_id',
            'format' => 'raw',
            'value' => static function (\common\models\Lead $model) {
                return $model->employee ? '<i class="fa fa-user"></i> ' . Html::encode($model->employee->username) : '-';
            },
            //'filter' => $userList
            ],
            [
            'attribute' => 'created',
            'value' => static function (\common\models\Lead $model) {
                return $model->created ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created)) : '-';
            },
            'format' => 'raw'
            ],
        // 'created:date',

        /*[
            'attribute' => 'updated',
            'value' => function(\common\models\Lead $model) {
                $str = '-';
                if($model->updated) {
                    $str = '<b>' . Yii::$app->formatter->asRelativeTime(strtotime($model->updated)) . '</b>';
                    $str .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated));
                }
                return $str;
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],*/

            [
            'attribute' => 'l_last_action_dt',
            'value' => function (\common\models\Lead $model) {
                $str = '-';
                if ($model->l_last_action_dt) {
                    $str = '<b>' . Yii::$app->formatter->asRelativeTime(strtotime($model->l_last_action_dt)) . '</b>';
                    $str .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->l_last_action_dt));
                }
                return $str;
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ],
            ],
        // 'bo_flight_id',

            [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{assign}',
            'controller' => 'case',
            'buttons' => [
                /*'view' => function ($url, $model, $key) {
                    return Html::a('<span class="fa fa-search"></span> View', ['sale/view', 'h' => base64_encode($model['confirmationNumber'] . '|' . $model['saleId'])],
                        ['title' => 'View', 'class' => 'btn btn-xs btn-info showModalCaseInfo', 'data-pjax' => 0]);
                },*/
                'assign' => function ($url, $model, $key) use ($caseModel) {
                    return Html::a(
                        '<span class="fa fa-check"></span> Assign',
                        ['cases/assign-lead'],
                        ['title' => 'Assign', 'class' => 'btn btn-xs btn-success assignLead', 'data-pjax' => 0, 'data-gid' => $caseModel->cs_gid, 'data-lead-gid' => $model->gid]
                    );
                },
            ]
            ]

    ];
    ?>

<?php
    echo \yii\grid\GridView::widget([
        'dataProvider' => $leadDataProvider,
        'filterModel' => false, //$isAgent ? false : $searchModel,
        'columns' => $gridColumns,
    ]);
    ?>
<?php Pjax::end(); ?>


<?php
Modal::end();

$jsCode = <<<JS
    $(document).on('click', '#search-lead-btn', function(){
        $('#modalLeadSearch').modal('show').find('#modalLeadSearchContent').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        $('#modalLeadSearch-label').html($(this).attr('title'));
       return false;
    });

    $(document).on('click', '.assignLead', function()
    {
        var btn = $(this);
        var lead_gid = btn.data('lead-gid');
        var gid = btn.data('gid');
        
        btn.addClass('disabled');
        btn.find('span').removeClass('fa-check').addClass('fa-spinner fa-spin');
        
        $.ajax({
            url: btn.attr('href'),
            type: 'post',
            data: {gid: gid, lead_gid: lead_gid},
            success: function (data) {
                if (data.error != '') {
                    alert('Error: ' + data.error);
                    btn.removeClass('disabled');
                    btn.find('span').removeClass('fa-spinner fa-spin').addClass('fa-check');
                    createNotifyByObject({
                        title: "Error add Lead",
                        type: "error",
                        text: 'Lead add sale in case',
                        hide: true
                    });
                } else {
                    btn.parent().parent().addClass('success');
                    btn.removeClass('disabled');
                    btn.find('span').removeClass('fa-spinner fa-spin').addClass('fa-check-circle-o');
                    $.pjax.reload({container: '#pjax-lead-info', push: false, replace: false, timeout: 10000, async: false});
                    createNotifyByObject({
                        title: "Lead successfully added",
                        type: "success",
                        text: 'Lead Id: ' + data.data.lead_id +' successfully added',
                        hide: true
                    });
                    $('#modalLeadSearch').modal('hide');
                }
                
            },
            error: function (error) {
                alert('Server Error');
                console.error('Error: ' + error);
                btn.removeClass('disabled');
                btn.find('span').removeClass('fa-spinner fa-spin').addClass('fa-check');                
            }
        });
        
       return false;
    });

JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
