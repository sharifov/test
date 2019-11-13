<?php

use sales\access\ListsAccess;
use sales\formatters\client\ClientTimeFormatter;
use yii\helpers\Html;
use yii\widgets\Pjax;
use common\models\Lead;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Duplicate Queue';

if (Yii::$app->user->identity->canRole('admin')) {
    $isAdmin = true;
} else {
    $isAdmin = false;
}

$lists = new ListsAccess(Yii::$app->user->id);

$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('/css/style-duplicate.css');
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
            'label' => 'Origin',
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
            'filter' => $lists->getProjects(),
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

        /*[
            // 'attribute' => 'client_id',
            'header' => 'Origin Request',
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
            ],
            'contentOptions' => [
                'class' => 'text-success'
            ],
        ],*/

        [
            // 'attribute' => 'client_id',
            'header' => 'Request Client Info',
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
            ],
            'contentOptions' => [
                'class' => 'text-danger'
            ],
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
                return ClientTimeFormatter::format($model->getClientTime2(), $model->offset_gmt);
            },
            'options' => [
                'style' => 'width:90px'
            ]
        ],




        /*[
            'label' => 'Origin Request Details',
            'content' => function (\common\models\Lead $model) {

                $parentModel = $model->lDuplicateLead;


                $content = '';
                if($parentModel) {
                    $content .= $parentModel->getFlightDetails();
                    $content .= ' (<i class="fa fa-male"></i> x' . ($parentModel->adults . '/' . $parentModel->children . '/' . $parentModel->infants) . ')<br/>';

                    $content .= sprintf('<strong>Cabin:</strong> %s', Lead::getCabin($parentModel['cabin']));
                }

                return $content;
            },
            'format' => 'raw',

            'contentOptions' => [
                'class' => 'text-success'
            ],
        ],*/

        [
            'label' => 'Request Details',
            'content' => function (\common\models\Lead $model) {
                $content = '';
                $content .= $model->getFlightDetails();
                $content .= ' (<i class="fa fa-male"></i> x' . ($model->adults . '/' . $model->children . '/' . $model->infants) . ')<br/>';

                $content .= sprintf('<strong>Cabin:</strong> %s', $model->getCabinClassName());

                return $content;
            },
            'format' => 'raw',
            'contentOptions' => [
                //'class' => 'text-danger'
            ],
            'options' => [
                'style' => 'width:220px'
            ],
        ],


        [
            'label' => 'Diff Request Details',
            'content' => function (\common\models\Lead $model) {
                $content = '';
                $content .= $model->getFlightDetails("\n")."\n";
                $content .= 'pax: ' . ($model->adults . '/' . $model->children . '/' . $model->infants) . ''."\n";

                $content .= sprintf('Cabin: %s', Lead::getCabin($model['cabin']));



                $parentModel = $model->lDuplicateLead;


                $contentParent = '';
                if($parentModel) {
                    $contentParent .= $parentModel->getFlightDetails("\n")."\n";
                    $contentParent .= 'pax: ' . ($parentModel->adults . '/' . $parentModel->children . '/' . $parentModel->infants) . ''."\n";

                    $contentParent .= sprintf('Cabin: %s', Lead::getCabin($parentModel['cabin']));
                }




                $options = array(
                    'context' => 3,
                    'ignoreNewLines' => false,
                    'ignoreWhitespace' => false,
                    'ignoreCase' => false
                );



                if (!is_array($content)) {
                    $lines1 = explode("\n", $content);
                }
                if (!is_array($contentParent)) {
                    $lines2 = explode("\n", $contentParent);
                }
                foreach ($lines1 as $i => $line) {
                    $lines1[$i] = rtrim($line, "\r\n");
                }
                foreach ($lines2 as $i => $line) {
                    $lines2[$i] = rtrim($line, "\r\n");
                }

                //\yii\helpers\VarDumper::dump($lines1);
                //$renderer = new \yii\gii\components\DiffRendererHtmlInline();
                /*$renderer = new Diff_Renderer_Html_SideBySide();*/
                $renderer = new Diff_Renderer_Html_Inline();

                $diff = new Diff($lines1, $lines2, $options);

                return $diff->render($renderer);


                //return $c; //$diff->Render($renderer);


                //return $content;
            },
            'format' => 'raw',
            'options' => [
                'style' => 'width:420px'
            ],
            'contentOptions' => [
                //'class' => 'text-warning'
            ],
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
            'filter' => $lists->getEmployees(),
            'visible' => $lists->getEmployees(),
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
            'label' => 'Reason',
            'contentOptions' => [
                'style' => 'max-width: 250px;'
            ],
            'value' => function (\common\models\Lead $model) {
                return '<pre>'. $model->getLastReasonFromLeadFlow()  . '</pre>';
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
            'template' => '{view} {view2} {delete}',
            'controller' => 'leads',

            'visibleButtons' => [
                    /*'view' => function ($model, $key, $index) {
                        return User::hasPermission('viewOrder');
                    },
                    'update' => function ($model, $key, $index) {
                        return User::hasPermission('updateOrder');
                    },*/
                    'delete' => function ($model, $key, $index) use ($isAdmin) {
                        return $isAdmin;
                    },
                    /*'soft-delete' => function ($model, $key, $index) {
                        return User::hasPermission('deleteOrder');
                    },*/
            ],

            'buttons' => [
                'view2' => function ($url, Lead $model) {
                    return Html::a('<i class="glyphicon glyphicon-search"></i>', [
                        'lead/view',
                        'gid' => $model->gid
                    ], [
                        'title' => 'View',
                    ]);
                },
                /*'soft-delete' => function ($url, $model) {
                    return Html::a('<i class="glyphicon glyphicon-remove-circle"></i>', $url, [
                        'title' => 'Delete',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this SMS?',
                            //'method' => 'post',
                        ],
                    ]);
                }*/
            ],

            /*'buttons' => [
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
            ]*/
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