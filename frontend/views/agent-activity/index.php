<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\Employee;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AgentActivitySearch */
/* @var $dataProvider yii\data\SqlDataProvider */

$this->title = 'Agents & Leads ( '.$searchModel->date_from.' - '.$searchModel->date_to.' )';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="lead-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-12">
        <?php Pjax::begin(); ?>
        <?php

        echo $this->render('_search', [
            'model' => $searchModel,
            'action' => 'index'
        ]);

        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false


            'columns' =>
                [
                    [
                        'label' => 'Agent',
                        'attribute' => 'username',
                        'value' => function($data) {
                            return '<b>'.Html::encode($data['username']).'</b>';
                        },
                        'format' => 'html',
                        'filter' => false,
                    ],
                    [
                        'label' => 'Inbound calls',
                        'attribute' => 'inbound_calls',
                        'value' => function($data) {
                            return $data['inbound_calls'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Outbound calls',
                        'attribute' => 'outbound_calls',
                        'value' => function($data) {
                            return $data['outbound_calls'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Calls length',
                        'attribute' => 'call_duration',
                        'value' => function($data) {
                            return $data['call_duration'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'SMS sent',
                        'attribute' => 'sms_sent',
                        'value' => function($data) {
                            return $data['sms_sent'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'SMS received',
                        'attribute' => 'sms_received',
                        'value' => function($data) {
                            return $data['sms_received'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Email sent',
                        'attribute' => 'email_sent',
                        'value' => function($data) {
                            return $data['email_sent'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Email received',
                        'attribute' => 'email_received',
                        'value' => function($data) {
                            return $data['email_received'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Quotes sent',
                        'attribute' => 'quotes_sent',
                        'value' => function($data) {
                            return $data['quotes_sent'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Processing',
                        'attribute' => 'st_processing',
                        'value' => function($data) {
                            return $data['st_processing'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Snooze',
                        'attribute' => 'st_snooze',
                        'value' => function($data) {
                            return $data['st_snooze'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'From Inbox to Processing',
                        'attribute' => 'inbox_processing',
                        'value' => function($data) {
                            return $data['inbox_processing'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'From Follow-up to Processing',
                        'attribute' => 'followup_processing',
                        'value' => function($data) {
                            return $data['followup_processing'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'From Processing to Trash',
                        'attribute' => 'processing_trash',
                        'value' => function($data) {
                            return $data['processing_trash'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'From Processing to Follow-up',
                        'attribute' => 'processing_followup',
                        'value' => function($data) {
                            return $data['processing_followup'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'From Processing to Snooze',
                        'attribute' => 'processing_snooze',
                        'value' => function($data) {
                            return $data['processing_snooze'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],


                    [
                        'label' => 'Cloned leads',
                        'attribute' => 'cloned_leads',
                        'value' => function($data) {
                            return $data['cloned_leads'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Tasks in pending',
                        'attribute' => 'tasks_pending',
                        'value' => function($data) {
                            return $data['tasks_pending'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Tasks Completed Nr+%',
                        'value' => function($data) {
                            if($data['total_tasks'] > 0) {
                                return $data['completed_tasks'].' => '. round($data['completed_tasks']*100/$data['total_tasks']).'%';
                            }
                            return '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        ],
                    [
                        'label' => 'Sold',
                        'attribute' => 'st_sold',
                        'value' => function($data) {
                            return $data['st_sold'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],
                    [
                        'label' => 'Profit',
                        'value' => function($data) use ($searchModel) {
                            $employee = Employee::findOne(['id' => $data['id']]);
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
                        'value' => function($data) {
                            return $data['created_leads'] ?: '-';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                    ],

                ],
            'pjax' => true,
            //'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
            //'bordered' => true,
            'striped' => false,
            'condensed' => false,
            'responsive' => true,
            'hover' => true,
            'floatHeader' => false,

        ]); ?>

        <?//php Pjax::end(); ?>
        </div>
    </div>
</div>
