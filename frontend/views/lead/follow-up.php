<?php
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\Lead;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $isAgent bool */


$this->title = 'Follow Up Queue';

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
<?php $showAll = Yii::$app->request->cookies->getValue(\common\models\Lead::getCookiesKey(), true);
$btnClass = (!$showAll)
? 'btn-warning' : 'btn-success';
$btnText = (!$showAll)
? 'Show All' : 'Show Unprocessed';
$btnUrl = Url::to(['lead/unprocessed', 'show' => !$showAll]);
echo Html::a($btnText, $btnUrl, [
    'class' => 'btn ' . $btnClass,
    'style' => 'margin-left: 10px;'
                        ]);?>
</h1>
<div class="lead-index">

    <?php Pjax::begin(); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>
    <?= $this->render('_search_follow_up', ['model' => $searchModel]); ?>

    <?php $form = \yii\bootstrap\ActiveForm::begin(['options' => ['data-pjax' => true]]); // ['action' => ['leads/update-multiple'] ?>

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
            'attribute' => 'created',
            'label' => 'Pending Time',
            'value' => function (\common\models\Lead $model) {
                return Yii::$app->formatter->asRelativeTime(strtotime($model->created)); //Lead::getPendingAfterCreate($model->created);
            },
            'format' => 'raw'
        ],

        [
            'attribute' => 'created',
            'value' => function (\common\models\Lead $model) {
                return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
            },
            'format' => 'raw',
            'filter' => false

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


                    $clientName.= '<br>'. $str;

                } else {
                    $clientName = '-';
                }

                return $clientName;
            },
            'options' => [
                'style' => 'width:160px'
            ]
        ],
        /*[
            'attribute' => 'clientTime',
            'label' => 'Client Time',
            'value' => function ($model) {
                return Lead::getClientTime($model['id']);
            },
            'format' => 'raw'
        ],*/

        [
            //'attribute' => 'client_id',
            'header' => 'Client time',
            'format' => 'raw',
            'value' => function(\common\models\Lead $model) {
                return $model->getClientTime2();
            },
            'options' => ['style' => 'width:160px'],
            //'filter' => \common\models\Employee::getList()
        ],

        [
            'attribute' => 'Request Details',
            'content' => function (\common\models\Lead $model) {
                $content = '';
                $content .= $model->getFlightDetails();
                $content .= ' (<i class="fa fa-male"></i> x' . ($model->adults .'/'. $model->children .'/'. $model->infants) . ')<br/>';

                $content .= sprintf('<strong>Cabin:</strong> %s', Lead::getCabin($model['cabin']));

                return $content;
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'Quotes ',
            'value' => function (\common\models\Lead $model) {
                $quotes = $model->getQuoteSendInfo();
                return sprintf('Total: <strong>%d</strong> <br> Sent: <strong>%d</strong>', ($quotes['send_q'] + $quotes['not_send_q']), $quotes['send_q']);
            },
            'format' => 'raw'
        ],
        /*[
            'attribute' => 'last_activity',
            'label' => 'Last Activity',
            'value' => function (\common\models\Lead $model) {
                return Lead::getLastActivity($model->getLastActivityByNote());
            },
            'format' => 'raw'
        ],*/

        [
            'attribute' => 'updated',
            'label' => 'Last Activity',
            'value' => function (\common\models\Lead $model) {
                return '<span title="'.Yii::$app->formatter->asDatetime(strtotime($model->updated)).'">'.Yii::$app->formatter->asRelativeTime(strtotime($model->updated)).'</span>';
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'reason',
            'label' => 'Reason',
            'contentOptions' => [
                'style' => 'max-width: 250px;'
            ],
            'value' => function (\common\models\Lead $model) {
                return $model->getLastReason();
            },
            'format' => 'raw'
        ],

        [
            'header' => 'Answered',
            'attribute' => 'l_answered',
            'value' => function (\common\models\Lead $model) {
                return $model->l_answered ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'filter' => [1 => 'Yes', 0 => 'No'],
            'format' => 'raw'
        ],

        [
            'header' => 'Grade',
            'attribute' => 'l_grade',
            'value' => function (\common\models\Lead $model) {
                return $model->l_grade;
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'visible' => ! $isAgent
        ],

        [
            'header' => 'Task Info',
            'value' => function (\common\models\Lead $model) {
                return '<small style="font-size: 10px">' . Lead::getTaskInfo2($model->id) . '</small>';
            },
            'format' => 'html',
            'contentOptions' => [
                'class' => 'text-left'
            ],
            'visible' => ! $isAgent,
            'options' => [
                'style' => 'width:140px'
            ]
        ],
        [
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
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{action}',
            'buttons' => [
                'action' => function ($url, \common\models\Lead $model, $key) {

                    $buttons = '';

                    $buttons .= Html::a('Take', Url::to([
                        'lead/take',
                        'id' => $model['id']
                    ]), [
                        'class' => 'btn btn-primary btn-xs take-btn',
                        'data-pjax' => 0
                    ]);

                    $buttons .= Html::a('<i class="fa fa-search"></i>', ['lead/quote', 'type' => 'processing', 'id' => $model->id], [
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
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'toolbar' => false,
    'pjax' => false,
    'striped' => true,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    'floatHeader' => true,
    'floatHeaderOptions' => [
        'scrollingTop' => 20
    ],
    /*'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Processing</h3>'
    ],*/

    'rowOptions' => function (Lead $model) {
        if ($model->status === Lead::STATUS_PROCESSING && Yii::$app->user->id == $model->employee_id) {
            return [
                'class' => 'highlighted'
            ];
        }

        /*if (in_array($model->status, [
            Lead::STATUS_ON_HOLD,
            Lead::STATUS_BOOKED,
            Lead::STATUS_FOLLOW_UP
        ])) {
            $now = new \DateTime();
            $departure = $model->getDeparture();

            $diff = ! empty($departure) ? $now->diff(new \DateTime($departure)) : $now->diff(new \DateTime($departure));
            $diffInSec = $diff->s + ($diff->i * 60) + ($diff->h * 3600) + ($diff->d * 86400) + ($diff->m * 30 * 86400) + ($diff->y * 12 * 30 * 86400);
            // if departure <= 7 days
            if ($diffInSec <= (7 * 24 * 60 * 60)) {
                return [
                    'class' => 'success'
                ];
            }
        }*/
    }

]);

?>


    <?php \yii\bootstrap\ActiveForm::end(); ?>


    <?php Pjax::end(); ?>


</div>

<?php
$js = <<<JS
    $('.take-processing-btn').click(function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if ($.inArray($(this).data('status'), [2, 8]) != -1) {
            var editBlock = $('#modal-error');
            editBlock.find('.modal-body').html('');
            editBlock.find('.modal-body').load(url, function( response, status, xhr ) {
                editBlock.modal('show');
            });
        } else {
            window.location = url;
        }
    });
JS;
$this->registerJs($js);

/*$this->registerJsFile('/js/moment.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
$this->registerJsFile('/js/moment-timezone-with-data.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);*/

$this->registerJsFile('/js/jquery.countdown-2.2.0/jquery.countdown.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);