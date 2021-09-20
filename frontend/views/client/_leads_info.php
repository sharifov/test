<?php

use common\models\Employee;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use sales\auth\Auth;
use yii\grid\GridView;
use yii\helpers\Html;
use common\models\Lead;
use common\models\LeadFlow;
use yii\helpers\Url;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var \common\models\Call|null $call */

/** @var Employee $user */
$user = Yii::$app->user->identity;

?>

<div class="row">
    <div class="col-md-12">
        <h4>Leads</h4>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
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
                    'attribute' => 'status',
                    'value' => static function (Lead $model) {
                        $statusValue = $model->getStatusName(true);
                        if ($model->isTrash() && ($lastLeadFlow = $model->lastLeadFlow)) {
                            if ($lastLeadFlow->status === $model->status && $lastLeadFlow->lf_description) {
                                $statusValue .= ' <span data-toggle="tooltip" data-placement="top" title="' . Html::encode($lastLeadFlow->lf_description) . '"><i class="fa fa-warning"></i></span>';
                            }
                        }
                        $statusLog = LeadFlow::find()->where([
                            'lead_id' => $model->id,
                            'status' => $model->status
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
                        return Yii::$app->formatter->asRelativeTime(strtotime($model->created));
                    },
                    'format' => 'raw'
                ],
                [
                    'label' => 'Project',
                    'attribute' => 'project_id',
                    'value' => static function (Lead $model) {
                        return $model->project ? $model->project->name : '-';
                    },
                ],
                [
                    'attribute' => 'cabin',
                    'value' => static function (Lead $model) {
                        return Lead::getCabin($model->cabin) ?: '-';
                    },
                ],
                [
                    'label' => 'Pax',
                    'value' => static function (Lead $model) {
                        return '<i class="fa fa-male"></i> <span title="adult">' . $model->adults . '</span> / <span title="child">' . $model->children . '</span>
        / <span title="infant">' . $model->infants . '</span>';
                    },
                    'format' => 'raw',
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                    'visible' => !($user->isAgent() || $user->isExAgent()),
                ],
                [
                    'header' => 'Quotes',
                    'value' => static function (Lead $model) {
                        if ($model->quotesCount) {
                            /** @var Employee $user */
                            $user = Yii::$app->user->identity;
                            if ($user->isAgent()) {
                                return $model->quotesCount;
                            }
                            return Html::a($model->quotesCount, ['quotes/index', 'QuoteSearch[lead_id]' => $model->id], ['target' => '_blank', 'data-pjax' => 0]);
                        }
                        return '-';
                    },
                    'format' => 'raw',
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                    'visible' => !($user->isAgent() || $user->isExAgent()),
                ],
                [
                    'header' => 'Segments',
                    'value' => static function (Lead $model) {
                        $segmentData = [];
                        foreach ($model->leadFlightSegments as $sk => $segment) {
                            $segmentData[] = ($sk + 1) . '. <code>' . Html::a($segment->origin . ' <i class="fa fa-long-arrow-right"></i> '
                                    . $segment->destination, [
                                    'lead-flight-segment/view',
                                    'id' => $segment->id
                                ], [
                                    'target' => '_blank',
                                    'data-pjax' => 0
                                ]) . '</code>';
                        }
                        return implode('<br>', $segmentData);
                    },
                    'format' => 'raw',
                    'contentOptions' => [
                        'class' => 'text-left'
                    ],
                    'options' => [
                        'style' => 'width:140px'
                    ],
                    'visible' => !($user->isAgent() || $user->isExAgent()),
                ],
                [
                    'header' => 'Depart',
                    'value' => static function (Lead $model) {
                        foreach ($model->leadFlightSegments as $sk => $segment) {
                            return date('d-M-Y', strtotime($segment->departure));
                        }
                        return '';
                    },
                    'format' => 'raw',
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                    'options' => [
                        'style' => 'width:100px'
                    ],
                    'visible' => !($user->isAgent() || $user->isExAgent()),
                ],
                [
                    'label' => 'Owner',
                    'attribute' => 'employee_id',
                    'format' => 'raw',
                    'value' => static function (Lead $model) {
                        return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
                    },
                ],
                [
                    'attribute' => 'created',
                    'value' => static function (Lead $model) {
                        return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'l_last_action_dt',
                    'value' => static function (Lead $model) {
                        return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->l_last_action_dt));
                    },
                    'format' => 'raw'
                ],

                [
                    'class' => \yii\grid\ActionColumn::class,
                    'visibleButtons' => [
                        'linkToCall' => static function (Lead $model, $key, $index) use ($call) {
                            $leadAbacDto = new LeadAbacDto($model, Auth::id());
                            return $call && $call->isStatusInProgress() && $call->c_lead_id !== $model->id && Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_LINK_TO_CALL, LeadAbacObject::ACTION_ACCESS);
                        },
                        'take' => static function (Lead $model, $key, $index) use ($call) {
                            $leadAbacDto = new LeadAbacDto($model, Auth::id());
                            return $call && $call->isStatusInProgress() && Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_TAKE_LEAD_FROM_CALL, LeadAbacObject::ACTION_ACCESS);
                        }
                    ],
                    'buttons' => [
                        'linkToCall' => static function ($url, Lead $model) use ($call) {
                            return Html::a(
                                '<i class="glyphicon glyphicon-link"></i> Link to call',
                                '#',
                                [
                                    'class' => 'btn btn-info btn-xs btn-link-lead-to-call',
                                    'data-pjax' => 0,
                                    'title' => 'Link to call',
                                    'data-call-id' => $call->c_id ?? null,
                                    'data-lead-id' => $model->id
                                ]
                            );
                        },
                        'take' => static function ($url, Lead $model) {
                            return Html::a('Take', '#', [
                                'class' => 'btn btn-primary btn-xs btn-lead-take',
                                'data-pjax' => 0,
                                'title' => 'Take',
                                'data-url' => Url::to(['/lead/take', 'gid' => $model->gid])
                            ]);
                        }
                    ],
                    'template' => '{linkToCall} {take}',
                ]
            ],
        ]) ?>

    </div>
</div>

<?php
$linkLeadToCallUrl = Url::to(['/lead/ajax-link-to-call']);
$urlGetClientInfo = Url::to(['/client/ajax-get-info', 'client_id' => $call->c_client_id, 'callSid' => $call->c_call_sid]);
$js = <<<JS
$('body').off('click', '.btn-lead-take').on('click', '.btn-lead-take', function (e) {
    e.preventDefault();
    let url = $(this).data('url');
    let leadWindow = window.open(url, '_blank');
    leadWindow.addEventListener('load', function () {
        pjaxReload({container: '#client_leads_info', push: false, replace: false, timeout: 5000, url: '$urlGetClientInfo'});
    });
    leadWindow.focus();
});
 $('body').off('click', '.btn-link-lead-to-call').on('click', '.btn-link-lead-to-call', function (e) {
    e.preventDefault();
    
    let btn = $(this);
    let btnHtml = btn.html();
    let leadId = btn.data('lead-id');
    let callId = btn.data('call-id');
    
    $.ajax({
        type: 'post',
        url: '$linkLeadToCallUrl',
        data: {leadId: leadId, callId: callId},
        dataType: 'json',
        beforeSend: function () {
            btn.html('<i class="fa fa-spin fa-spinner" />').addClass('disabled');
        },
        success: function (data) {
            if (data.error) {
                createNotify('Error', data.message, 'error');
            } else {
                createNotify('Success', 'Lead successfully assigned to call', 'success');
                pjaxReload({container: '#client_leads_info', push: false, replace: false, timeout: 5000, url: '$urlGetClientInfo'});
            }
        },
        complete: function () {
            btn.html(btnHtml).removeClass('disabled');
        },
        error: function (xhr) {
            createNotify('Error', xhr.responseText, 'error');
        }
    })
});
JS;

$this->registerJs($js);
