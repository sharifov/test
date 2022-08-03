<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\Employee;
use yii\bootstrap4\Modal;
use common\models\Call;
use common\models\Sms;
use common\models\Lead;
use src\entities\email\helpers\EmailType;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AgentActivitySearch */
/* @var $dataProvider yii\data\SqlDataProvider */

$this->title = 'Agents Report ( ' . $searchModel->date_from . ' - ' . $searchModel->date_to . ' )';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("$(function() {
   $('.popupModal').click(function(e) {
     e.preventDefault();
    $('#details__modal-label').html($(this).data('title'));
     $('#details__modal').modal('show')
         .find('.modal-body')
         .load($(this).attr('href'));
   });
});");
?>
<div class="agent-report">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-12">
        <?php
        echo $this->render('_search', [
            'model' => $searchModel,
            'action' => 'index'
        ]);

        Pjax::begin(['id' => 'agent-activity', 'timeout' => 10000, 'scrollTo' => 0]);
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{errors}\n{summary}\n{items}\n{pager}",
            'filterModel' => $searchModel,
            //'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false


            'columns' =>
            [
                    [
                        'label' => 'Agent ID',
                        'attribute' => 'id',
                        'filter' => false,
                    ],
                    /*[
                        'label' => 'Agent',
                        'attribute' => 'username',
                        'value' => static function ($data) {
                            return '<b>'.Html::encode($data['username']).'</b>';
                        },
                        'format' => 'raw',
                        'filter' => true,
                    ],*/

                    'username:userName',

                    [
                        'label' => 'Inbound calls',
                        'attribute' => 'inbound_calls',
                        'value' => function ($data) use ($searchModel) {
                            if (!$data['inbound_calls']) {
                                return '-';
                            }
                            return \yii\bootstrap\Html::a(
                                $data['inbound_calls'],
                                ['/agent-report/calls',
                                    $searchModel->formName() . '[c_call_type_id]' => Call::CALL_TYPE_IN,
                                    $searchModel->formName() . '[id]' => $data['id'],
                                    $searchModel->formName() . '[date_from]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_from)),
                                    $searchModel->formName() . '[date_to]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_to))],
                                ['target' => '_blank', 'data-pjax' => 0]
                            );
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Outbound calls',
                        'attribute' => 'outbound_calls',
                        'value' => function ($data) use ($searchModel) {
                            if (!$data['outbound_calls']) {
                                return '-';
                            }
                            return \yii\bootstrap\Html::a(
                                $data['outbound_calls'],
                                ['/agent-report/calls',
                                    $searchModel->formName() . '[c_call_type_id]' => Call::CALL_TYPE_OUT,
                                    $searchModel->formName() . '[id]' => $data['id'],
                                    $searchModel->formName() . '[date_from]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_from)),
                                    $searchModel->formName() . '[date_to]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_to))],
                                ['target' => '_blank', 'data-pjax' => 0]
                            );
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Calls length',
                        'attribute' => 'call_duration',
                        'value' => function ($data) {
                            $part1 = '';
                            if (!$data['call_duration']) {
                                $part1 = '-';
                            } else {
                                $hours = floor($data['call_duration'] / 3600);
                                $mins = floor($data['call_duration'] / 60 % 60);
                                $secs = floor($data['call_duration'] % 60);

                                $part1 = '<span title="' . Yii::$app->formatter->asDuration($data['call_duration']) . '">' . sprintf('%02d:%02d:%02d', $hours, $mins, $secs) . '</span>';
                            }

                            $part2 = '';
                            if (!$data['call_recording_duration']) {
                                $part2 = '-';
                            } else {
                                $hours = floor($data['call_recording_duration'] / 3600);
                                $mins = floor($data['call_recording_duration'] / 60 % 60);
                                $secs = floor($data['call_recording_duration'] % 60);

                                $part2 = '<span title="' . Yii::$app->formatter->asDuration($data['call_recording_duration']) . '">' . sprintf('%02d:%02d:%02d', $hours, $mins, $secs) . '</span>';
                            }

                            return $part1 . ' / ' . $part2;
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'SMS sent',
                        'attribute' => 'sms_sent',
                        'value' => function ($data) use ($searchModel) {
                            if (!$data['sms_sent']) {
                                return '-';
                            }
                            return \yii\bootstrap\Html::a(
                                $data['sms_sent'],
                                ['/agent-report/sms',
                                    $searchModel->formName() . '[s_type_id]' => Sms::TYPE_OUTBOX,
                                    $searchModel->formName() . '[id]' => $data['id'],
                                    $searchModel->formName() . '[date_from]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_from)),
                                    $searchModel->formName() . '[date_to]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_to))],
                                ['target' => '_blank', 'data-pjax' => 0]
                            );
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'SMS received',
                        'attribute' => 'sms_received',
                        'value' => function ($data) use ($searchModel) {
                            if (!$data['sms_received']) {
                                return '-';
                            }
                            return \yii\bootstrap\Html::a(
                                $data['sms_received'],
                                ['/agent-report/sms',
                                    $searchModel->formName() . '[s_type_id]' => Sms::TYPE_INBOX,
                                    $searchModel->formName() . '[id]' => $data['id'],
                                    $searchModel->formName() . '[date_from]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_from)),
                                    $searchModel->formName() . '[date_to]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_to))],
                                ['target' => '_blank', 'data-pjax' => 0]
                            );
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Email sent',
                        'attribute' => 'email_sent',
                        'value' => function ($data) use ($searchModel) {
                            if (!$data['email_sent']) {
                                return '-';
                            }
                            return \yii\bootstrap\Html::a(
                                $data['email_sent'],
                                ['/agent-report/email',
                                    $searchModel->formName() . '[e_type_id]' => EmailType::OUTBOX,
                                    $searchModel->formName() . '[id]' => $data['id'],
                                    $searchModel->formName() . '[date_from]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_from)),
                                    $searchModel->formName() . '[date_to]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_to))],
                                ['target' => '_blank', 'data-pjax' => 0]
                            );
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Email received',
                        'attribute' => 'email_received',
                        'value' => function ($data) use ($searchModel) {
                            if (!$data['email_received']) {
                                return '-';
                            }
                            return \yii\bootstrap\Html::a(
                                $data['email_received'],
                                ['/agent-report/email',
                                    $searchModel->formName() . '[e_type_id]' => EmailType::INBOX,
                                    $searchModel->formName() . '[id]' => $data['id'],
                                    $searchModel->formName() . '[date_from]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_from)),
                                    $searchModel->formName() . '[date_to]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_to))],
                                ['target' => '_blank', 'data-pjax' => 0]
                            );
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Quotes sent',
                        'attribute' => 'quotes_sent',
                        'value' => function ($data) {
                            return $data['quotes_sent'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Processing',
                        'attribute' => 'st_processing',
                        'value' => function ($data) {
                            return $data['st_processing'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Snooze',
                        'attribute' => 'st_snooze',
                        'value' => function ($data) {
                            return $data['st_snooze'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'From Inbox to Processing',
                        'attribute' => 'inbox_processing',
                        'value' => function ($data) use ($searchModel) {
                            if (!$data['inbox_processing']) {
                                return '-';
                            }
                            return \yii\bootstrap\Html::a(
                                $data['inbox_processing'],
                                ['/agent-report/from-to-leads',
                                    'title' => 'From Inbox to Processing',
                                    $searchModel->formName() . '[from_status]' => Lead::STATUS_PENDING,
                                    $searchModel->formName() . '[to_status]' => Lead::STATUS_PROCESSING,
                                    $searchModel->formName() . '[id]' => $data['id'],
                                    $searchModel->formName() . '[date_from]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_from)),
                                    $searchModel->formName() . '[date_to]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_to))],
                                ['target' => '_blank', 'data-pjax' => 0]
                            );
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw'
                    ],
                    [
                        'label' => 'From Follow-up to Processing',
                        'attribute' => 'followup_processing',
                        'value' => function ($data) use ($searchModel) {
                            if (!$data['followup_processing']) {
                                return '-';
                            }
                                return \yii\bootstrap\Html::a(
                                    $data['followup_processing'],
                                    ['/agent-report/from-to-leads',
                                        'title' => 'From Follow-up to Processing',
                                        $searchModel->formName() . '[from_status]' => Lead::STATUS_FOLLOW_UP,
                                        $searchModel->formName() . '[to_status]' => Lead::STATUS_PROCESSING,
                                        $searchModel->formName() . '[id]' => $data['id'],
                                        $searchModel->formName() . '[date_from]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_from)),
                                        $searchModel->formName() . '[date_to]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_to))],
                                    ['target' => '_blank', 'data-pjax' => 0]
                                );
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw'
                    ],
                    [
                        'label' => 'From Processing to Trash',
                        'attribute' => 'processing_trash',
                        'value' => function ($data) use ($searchModel) {
                            if (!$data['processing_trash']) {
                                return '-';
                            }
                                return \yii\bootstrap\Html::a(
                                    $data['processing_trash'],
                                    ['/agent-report/from-to-leads',
                                        'title' => 'From Processing to Trash',
                                        $searchModel->formName() . '[from_status]' => Lead::STATUS_PROCESSING,
                                        $searchModel->formName() . '[to_status]' => Lead::STATUS_TRASH,
                                        $searchModel->formName() . '[id]' => $data['id'],
                                        $searchModel->formName() . '[date_from]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_from)),
                                        $searchModel->formName() . '[date_to]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_to))],
                                    ['target' => '_blank', 'data-pjax' => 0]
                                );
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw'
                    ],
                    [
                        'label' => 'From Processing to Follow-up',
                        'attribute' => 'processing_followup',
                        'value' => function ($data) use ($searchModel) {
                            if (!$data['processing_followup']) {
                                return '-';
                            }
                                return \yii\bootstrap\Html::a(
                                    $data['processing_followup'],
                                    ['/agent-report/from-to-leads',
                                        'title' => 'From Processing to Follow-up',
                                        $searchModel->formName() . '[from_status]' => Lead::STATUS_PROCESSING,
                                        $searchModel->formName() . '[to_status]' => Lead::STATUS_FOLLOW_UP,
                                        $searchModel->formName() . '[id]' => $data['id'],
                                        $searchModel->formName() . '[date_from]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_from)),
                                        $searchModel->formName() . '[date_to]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_to))],
                                    ['target' => '_blank', 'data-pjax' => 0]
                                );
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw'
                    ],
                    [
                        'label' => 'From Processing to Snooze',
                        'attribute' => 'processing_snooze',
                        'value' => function ($data) use ($searchModel) {
                            if (!$data['processing_snooze']) {
                                return '-';
                            }
                                return \yii\bootstrap\Html::a(
                                    $data['processing_snooze'],
                                    ['/agent-report/from-to-leads',
                                        'title' => 'From Processing to Snooze',
                                        $searchModel->formName() . '[from_status]' => Lead::STATUS_PROCESSING,
                                        $searchModel->formName() . '[to_status]' => Lead::STATUS_SNOOZE,
                                        $searchModel->formName() . '[id]' => $data['id'],
                                        $searchModel->formName() . '[date_from]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_from)),
                                        $searchModel->formName() . '[date_to]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_to))],
                                    ['target' => '_blank', 'data-pjax' => 0]
                                );
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw'
                    ],
                    [
                        'label' => 'Extra Q Taken Leads',
                        'attribute' => 'extra_q_to_processing_cnt',
                        'value' => function ($data) use ($searchModel) {
                            if (!$data['extra_q_to_processing_cnt']) {
                                return '-';
                            }
                                return \yii\bootstrap\Html::a(
                                    $data['extra_q_to_processing_cnt'],
                                    ['/agent-report/from-to-leads',
                                        'title' => 'From Processing to Snooze',
                                        $searchModel->formName() . '[from_status]' => Lead::STATUS_EXTRA_QUEUE,
                                        $searchModel->formName() . '[to_status]' => Lead::STATUS_PROCESSING,
                                        $searchModel->formName() . '[lf_owner_id]' => $data['id'],
                                        $searchModel->formName() . '[date_from]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_from)),
                                        $searchModel->formName() . '[date_to]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_to))],
                                    ['target' => '_blank', 'data-pjax' => 0]
                                );
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Extra Q Lost Leads',
                        'attribute' => 'processing_to_extra_q_cnt',
                        'value' => function ($data) use ($searchModel) {
                            if (!$data['processing_to_extra_q_cnt']) {
                                return '-';
                            }
                                return \yii\bootstrap\Html::a(
                                    $data['processing_to_extra_q_cnt'],
                                    ['/agent-report/from-to-leads',
                                        'title' => 'From Processing to Snooze',
                                        $searchModel->formName() . '[from_status]' => Lead::STATUS_PROCESSING,
                                        $searchModel->formName() . '[to_status]' => Lead::STATUS_EXTRA_QUEUE,
                                        $searchModel->formName() . '[id]' => $data['id'],
                                        $searchModel->formName() . '[isExtraQLostLeads]' => true,
                                        $searchModel->formName() . '[date_from]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_from)),
                                        $searchModel->formName() . '[date_to]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_to))],
                                    ['target' => '_blank', 'data-pjax' => 0]
                                );
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw',
                    ],

                    [
                        'label' => 'Cloned leads',
                        'attribute' => 'cloned_leads',
                        'value' => function ($data) use ($searchModel) {
                            if (!$data['cloned_leads']) {
                                return '-';
                            }
                            return \yii\bootstrap\Html::a(
                                $data['cloned_leads'],
                                ['/agent-report/cloned',
                                    $searchModel->formName() . '[id]' => $data['id'],
                                    $searchModel->formName() . '[date_from]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_from)),
                                    $searchModel->formName() . '[date_to]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_to))],
                                ['target' => '_blank', 'data-pjax' => 0]
                            );
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw'
                    ],
                    [
                        'label' => 'Tasks in pending',
                        'attribute' => 'tasks_pending',
                        'value' => function ($data) {
                            return $data['tasks_pending'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Tasks Completed Nr+%',
                        'attribute' => 'completed_tasks',
                        'value' => function ($data) {
                            if ($data['total_tasks'] > 0) {
                                return $data['completed_tasks'] . ' (' . round($data['completed_tasks'] * 100 / $data['total_tasks']) . '%)';
                            }
                            return '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        ],
                    [
                        'label' => 'Sold',
                        'attribute' => 'st_sold',
                        'value' => function ($data) use ($searchModel) {
                            if (!$data['st_sold']) {
                                return '-';
                            }
                            return \yii\bootstrap\Html::a(
                                $data['st_sold'],
                                ['/agent-report/sold',
                                    $searchModel->formName() . '[id]' => $data['id'],
                                    $searchModel->formName() . '[date_from]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_from)),
                                    $searchModel->formName() . '[date_to]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_to))],
                                ['target' => '_blank', 'data-pjax' => 0]
                            );
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw'
                    ],
                    [
                        'label' => 'Profit',
                        'value' => function ($data) use ($searchModel) {
                            $employee = Employee::findOne($data['id']);
                            $from = new DateTime($searchModel->date_from);
                            $to = new DateTime($searchModel->date_to);
                            $salary = $employee->calculateSalaryBetween($from, $to);
                            return $salary['startProfit'];
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Created leads',
                        'attribute' => 'created_leads',
                        'value' => function ($data) use ($searchModel) {
                            if (!$data['created_leads']) {
                                return '-';
                            }
                            return \yii\bootstrap\Html::a(
                                $data['created_leads'],
                                ['/agent-report/created',
                                    $searchModel->formName() . '[id]' => $data['id'],
                                    $searchModel->formName() . '[date_from]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_from)),
                                    $searchModel->formName() . '[date_to]' => Employee::convertTimeFromUserDtToUTC(strtotime($searchModel->date_to))],
                                ['target' => '_blank', 'data-pjax' => 0]
                            );
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => 'raw'
                    ],

                ],
            'pjax' => false,
            //'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
            //'bordered' => true,
            'striped' => false,
            'condensed' => false,
            'responsive' => true,
            'hover' => true,
            'floatHeader' => false,

        ]); ?>

        <?php Pjax::end(); ?>
        </div>
    </div>
</div>
<?php Modal::begin(['id' => 'details__modal',
    'title' => '',
    'size' => Modal::SIZE_LARGE,
])?>
<?php Modal::end()?>