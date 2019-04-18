<?php
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\Lead;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Duplicate Queue';

if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
    $projectList = \common\models\Project::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
    $projectList = \common\models\ProjectEmployeeAccess::getProjectsByEmployee();
}

$this->params['breadcrumbs'][] = $this->title;
?>

<style>
.dropdown-menu {
	z-index: 1010 !important;
}
</style>
<h1>
	<?=\yii\helpers\Html::encode($this->title)?>
</h1>
<div class="lead-duplicate">

    <?php Pjax::begin(); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>

    <?php

    $gridColumns = [
        [
            'attribute' => 'id',
            'label' => 'Lead ID',
            'value' => function (\common\models\Lead $model) {
                return $model->id;
            },
            'options' => [
                'style' => 'width:80px'
            ]
        ],

        [
            'attribute' => 'l_duplicate_lead_id',
            'label' => 'Duplicate from',
            'value' => function (\common\models\Lead $model) {
                return $model->l_duplicate_lead_id ? Html::a($model->l_duplicate_lead_id, ['/leads/view', 'id' => $model->l_duplicate_lead_id], ['data-pjax' => 0, 'target' => '_blank']) : '-';
            },
            'format' => 'raw',
            'options' => [
                'style' => 'width:120px'
            ]
        ],
        'l_request_hash',

        [
            'attribute' => 'project_id',
            'value' => function (\common\models\Lead $model) {
                return $model->project ? '<span class="badge badge-info">' . $model->project->name . '</span>' : '-';
            },
            'format' => 'raw',
            'options' => [
                'style' => 'width:120px'
            ],
            'filter' => $projectList,
            //'visible' => ! $isAgent
        ],
        [
            'attribute' => 'pending',
            'label' => 'Pending Time',
            'value' => function (\common\models\Lead $model) {

                $str = Yii::$app->formatter->asRelativeTime(strtotime($model->created));
                $str .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));

                return $str;
            },
            'options' => [
                'style' => 'width:160px'
            ],
            'format' => 'raw'
        ],

        /*[
            'attribute' => 'created',
            'value' => function (\common\models\Lead $model) {
                return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
            },
            'format' => 'raw',
            'filter' => false

        ],*/

        [
            // 'attribute' => 'client_id',
            'header' => 'Request',
            'format' => 'raw',
            'value' => function (\common\models\Lead $model) {

                $clientName = trim($model->l_client_first_name . ' ' . $model->l_client_last_name);

                if ($clientName) {
                    $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName).'';
                }

                $str = $model->l_client_email ? '<br><i class="fa fa-envelope"></i> ' . $model->l_client_email : '';
                $str .= $model->l_client_phone ? '<br><i class="fa fa-phone"></i>' . $model->l_client_phone : '';
                $clientName .= $str;

                return $clientName;
            },

            'options' => [
                'style' => 'width:160px'
            ]
        ],

        [
            // 'attribute' => 'client_id',
            'header' => 'Client',
            'format' => 'raw',
            'value' => function (\common\models\Lead $model) {

                if ($model->client) {
                    $clientName = $model->client->first_name . ' ' . $model->client->last_name;
                    if ($clientName === 'Client Name') {
                        $clientName = '- - - ';
                    } else {
                        $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                    }

                    $str = $model->client && $model->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')) . '' : '';
                    $str .= $model->client && $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) . '' : '';

                    $clientName .= '<br>' . $str;
                } else {
                    $clientName = '-';
                }

                return $clientName;
            },
            'options' => [
                'style' => 'width:220px'
            ]
        ],/*
        [
            'attribute' => 'clientTime',
            'label' => 'Client Time',
            'value' => function ($model) {
                return Lead::getClientTime($model['id']);
            },
            'format' => 'raw'
        ], */

        [
            'header' => 'Client time',
            'format' => 'raw',
            'value' => function (\common\models\Lead $model) {
                return $model->getClientTime2();
            },
            'options' => [
                'style' => 'width:110px'
            ]
        ],

        [
            'attribute' => 'Request Details',
            'content' => function (\common\models\Lead $model) {
                $content = '';
                $content .= $model->getFlightDetails();
                $content .= ' (<i class="fa fa-male"></i> x' . ($model->adults . '/' . $model->children . '/' . $model->infants) . ')<br/>';

                $content .= sprintf('<strong>Cabin:</strong> %s', Lead::getCabin($model['cabin']));

                return $content;
            },
            'format' => 'raw'
        ],
//        [
//            'attribute' => 'Quotes ',
//            'value' => function (\common\models\Lead $model) {
//                $quotes = $model->getQuoteSendInfo();
//                return sprintf('Total: <strong>%d</strong> <br> Sent: <strong>%d</strong>', ($quotes['send_q'] + $quotes['not_send_q']), $quotes['send_q']);
//            },
//            'format' => 'raw'
//        ],

        [
            'attribute' => 'Quotes',
            'value' => function (\common\models\Lead $model) {
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
            'value' => function (\common\models\Lead $model) {
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
            'value' => function (\common\models\Lead $model) {
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
            'value' => function (\common\models\Lead $model) {
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

        [
            //'header' => 'Agent',
            'attribute' => 'employee_id',
            'format' => 'raw',
            'value' => function (\common\models\Lead $model) {
                return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
            },
            'filter' => $userList,
            //'visible' => ! $isAgent
        ],
        /*[
            'attribute' => 'update',
            'label' => 'Last Update',
            'value' => function (\common\models\Lead $model) {
                return '<span title="' . Yii::$app->formatter->asDatetime(strtotime($model->updated)) . '">' . Yii::$app->formatter->asRelativeTime(strtotime($model->updated)) . '</span>';
            },
            'format' => 'raw'
        ],*/
        [
            //'attribute' => 'reason',
            'label' => 'Reason',
            'contentOptions' => [
                'style' => 'max-width: 250px;'
            ],
            'value' => function (\common\models\Lead $model) {
                return '<pre>'.$model->getLastReason().'</pre>';
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
            'value' => function (\common\models\Lead $model) {
                return Lead::getRating2($model->rating);
            },
            'format' => 'raw'
        ],*/

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{action}',
            'buttons' => [
                'action' => function ($url, \common\models\Lead $model, $key) {

                    $buttons = '';

                    $buttons .= Html::a('<i class="fa fa-search"></i> view', [
                        'leads/view',
                        'id' => $model->id
                    ], [
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

echo \yii\grid\GridView::widget([
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