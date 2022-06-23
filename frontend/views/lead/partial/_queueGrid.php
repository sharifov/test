<?php

/**
 * @var $this \yii\web\View
 * @var $dataProvider ActiveDataProvider
 * @var $searchModel Lead
 * @var $div string
 */

use src\services\parsingDump\ReservationService;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use common\models\Lead;
use yii\helpers\Url;
use common\models\Quote;
use common\models\Employee;

$actionButtonTemplate = '{action}';
$queueType = Yii::$app->request->get('type');

/** @var Employee $user */
$user = Yii::$app->user->identity;

$is_manager = false;
if ($user->isAdmin() || $user->isSupervision()) {
    $is_manager = true;
}

?>
<?php \yii\widgets\Pjax::begin(['timeout' => 10000]); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    //'layout' => $template,
    'filterModel' => $searchModel,
    'rowOptions' => function ($model) {
        if (
            $model['status'] === Lead::STATUS_PROCESSING &&
            Yii::$app->user->identity->getId() == $model['employee_id']
        ) {
            return ['class' => 'highlighted'];
        }
        if (in_array($model['status'], [Lead::STATUS_ON_HOLD, Lead::STATUS_BOOKED, Lead::STATUS_FOLLOW_UP])) {
            $now = new \DateTime();
            $diff = !empty($model['departure'])
                ? $now->diff(new \DateTime($model['departure']))
                : $now->diff(new \DateTime($model['created']));
            $diffInSec = $diff->s + ($diff->i * 60) + ($diff->h * 3600) + ($diff->d * 86400) + ($diff->m * 30 * 86400) + ($diff->y * 12 * 30 * 86400);
            //if departure <= 7 days
            if ($diffInSec <= (7 * 24 * 60 * 60)) {
                return ['class' => 'success'];
            }
        }
    },
    'columns' => [
        [
            'attribute' => 'id',
            'label' => 'Lead Id',
            'visible' => $is_manager,
            'value' => static function ($model) {
                return $model['id'];
            },
        ],

        [
            'attribute' => 'bo_flight_id',
            'label' => 'Sale ID (BO)',
            'visible' => in_array($queueType, ['booked', 'sold']),
            'value' => static function ($model) {
                return $model['bo_flight_id'];
            },
        ],

        [
            'attribute' => 'pending',
            'label' => 'Pending Time',
            'visible' => !in_array($queueType, ['sold', 'booked']),
            'value' => static function ($model) {
                return Yii::$app->formatter->asRelativeTime(strtotime($model['created'])); //Lead::getPendingAfterCreate($model['created']);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'pending_last_status',
            'label' => 'Pending Time',
            'visible' => in_array($queueType, ['sold', 'booked']),
            'value' => static function ($model) {
                return Lead::getPendingInLastStatus($model['updated']);
            },
            'format' => 'raw'
        ],

        [
            'attribute' => 'created',
            //'label' => 'Created Date',
            'visible' => !in_array($queueType, ['booked', 'sold']),
            'value' => static function ($model) {
                return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model['created']));
            },
            'format' => 'html',
            'filter' => false

        ],
        [
            'label' => 'PNR',
            'visible' => in_array($queueType, ['booked', 'sold']),
            'value' => static function ($model) {
                if (!empty($model['additional_information'])) {
                    $additionally = new \common\models\local\LeadAdditionalInformation();
                    $additionally->setAttributes(@json_decode($model['additional_information'], true));
                    return (!empty($additionally->pnr))
                        ? $additionally->pnr : '-';
                }
                return '-';
            }
        ],
        [
            'label' => 'Passengers',
            'visible' => in_array($queueType, ['booked', 'sold']),
            'value' => static function ($model) {
                $content = [];
                if (!empty($model['additional_information'])) {
                    $additionally = new \common\models\local\LeadAdditionalInformation();
                    $additionally->setAttributes(@json_decode($model['additional_information'], true));
                    $content = (!empty($additionally->passengers))
                        ? $additionally->passengers : $content;
                }
                return implode('<br/>', $content);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'Client',
            'visible' => !in_array($queueType, ['booked']),
            'value' => static function ($model) {

                if (isset($model['first_name'])) {
                    $clientName = $model['first_name'] . ' ' . $model['last_name'];
                    if ($clientName === 'Client Name') {
                        $clientName = '- - - ';
                    } else {
                        $clientName = '<i class="glyphicon glyphicon-user"></i> ' . Html::encode($clientName);
                    }
                } else {
                    $clientName = '-';
                }
                return $clientName;
                //return '<i class="glyphicon glyphicon-user"></i> ' . $model['first_name']. ' ' .$model['last_name'];
            },
            'format' => 'html'
        ],
        [
            'label' => 'Client Email',
            'visible' => in_array($queueType, ['sold']),
            'value' => static function ($model) {
                return !empty($model['emails'])
                    ? $model['emails'] : '---';
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Client Phone',
            'visible' => in_array($queueType, ['trash', 'sold']),
            'value' => static function ($model) {
                /**
                 * @var $model Lead
                 */
                return !empty($model['phones'])
                    ? $model['phones'] : '---';
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'clientTime',
            'label' => 'Client Time',
            'visible' => !in_array($queueType, ['booked', 'sold']),
            'value' => static function (Lead $model) {
                return $model->getClientTime($model['id']);
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Destination',
            'visible' => in_array($queueType, ['booked', 'sold']),
            'value' => static function ($model) {
                return empty($model['destination'])
                    ? null : sprintf('%s (%s)', $model['city'], $model['destination']);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'Request Details',
            'visible' => !in_array($queueType, ['booked', 'sold']),
            'content' => function ($model) use ($queueType) {
                $content = '';
                if ($queueType === 'inbox' && Yii::$app->user->identity->canRole('agent')) {
                    $content .= '';
                } else {
                    $content .= $model['flight_detail'];
                    $content .= ' (<i class="fa fa-male"></i> x' . ($model['adults'] + $model['children'] + $model['infants']) . ')<br/>';
                }

                $content .= sprintf('<strong>Cabin:</strong> %s', Lead::getCabin($model['cabin']));

                return $content;
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'Quotes ',
            'visible' => !in_array($queueType, ['booked', 'inbox', 'sold']),
            'value' => static function ($model) {
                /**
                 * @var $model Lead
                 */
                return sprintf(
                    'Total: <strong>%d</strong> <br> Sent: <strong>%d</strong>',
                    ($model['send_q'] + $model['not_send_q']),
                    $model['send_q']
                );
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'pending_in_trash',
            'label' => 'Pending in Trash',
            'visible' => in_array($queueType, ['trash']),
            'value' => static function ($model) {
                return Lead::getPendingInLastStatus($model['updated']);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'agent',
            'label' => 'Agent',
            'filter' => ($searchModel !== null)
                ? Html::activeDropDownList($searchModel, 'employee_id', Employee::getAllEmployees(), [
                    'prompt' => '',
                    'class' => 'form-control'
                ])
                : null,
            'visible' => !in_array($queueType, ['inbox', 'follow-up']),
            'value' => static function ($model) {
                return !empty($model['username']) ? '<i class="fa fa-user"></i> ' . Html::encode($model['username']) : '-';
            },
            'format' => 'html'
        ],
        [
            'label' => 'Profit',
            'visible' => in_array($queueType, ['booked', 'sold']),
            'value' => static function ($model) {
                $profit = Quote::getProfit($model['mark_up'], $model['selling'], $model['fare_type'], boolval($model['check_payment']));
                return sprintf('<strong>$%s</strong>', number_format($profit, 2));
                /*$profit = 0;
                 if (!empty($model['mark_up'])) {
                    $profit = $model['mark_up'] - ($model['selling'] * Quote::SERVICE_FEE);
                    $profit = ($profit < 0) ? 0 : $profit;
                }
                return sprintf('$%s', number_format($profit, 2));*/
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Date of Issue',
            'visible' => in_array($queueType, ['sold']),
            'value' => static function ($model) {
                return $model['updated'];
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Date of Departure',
            'visible' => in_array($queueType, ['sold']),
            'value' => static function ($model) {
                if (isset($model['reservation_dump']) && !empty($model['reservation_dump'])) {
                    $data = [];
                    $segments = Quote::parseDump($model['reservation_dump'], false, $data, true);
                    return $segments[0]['departureDateTime']->format('Y-m-d H:i');
                }
                return $model['departure'];
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Processing Status',
            'visible' => in_array($queueType, ['booked']),
            'value' => static function ($model) {
                $additionally = new \common\models\local\LeadAdditionalInformation();
                if (!empty($model['additional_information'])) {
                    $additionally->setAttributes(@json_decode($model['additional_information'], true));
                }
                $labelVTF = '<span class="label label-danger"><i class="fa fa-times"></i></span>';
                if (!empty($additionally->vtf_processed)) {
                    $labelVTF = '<span class="label label-success"><i class="fa fa-check"></i></span>';
                }
                $labelTKT = '<span class="label label-danger"><i class="fa fa-times"></i></span>';
                if (!empty($additionally->tkt_processed)) {
                    $labelTKT = '<span class="label label-success"><i class="fa fa-check"></i></span>';
                }
                $labelEXP = '<span class="label label-danger"><i class="fa fa-times"></i></span>';
                if (!empty($additionally->exp_processed)) {
                    $labelEXP = '<span class="label label-success"><i class="fa fa-check"></i></span>';
                }
                return 'VTF: ' . $labelVTF . ' TKT: ' . $labelTKT . ' EXP: ' . $labelEXP;
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Status',
            'visible' => !in_array($queueType, ['sold']),
            'value' => static function (Lead $model) {
                return $model->getStatusLabel($model['status']);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'last_activity',
            'label' => 'Last Activity',
            'visible' => !in_array($queueType, ['inbox', 'sold']),
            'value' => static function ($model) {
                if (empty($model['last_activity'])) {
                    return '-';
                }
                return Lead::getLastActivity($model['last_activity']);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'reason',
            'label' => 'Reason',
            'visible' => !in_array($queueType, ['inbox', 'sold', 'booked']),
            'contentOptions' => ['style' => 'max-width: 250px;'],
            'value' => static function ($model) {
                return !empty($model['reason']) ? $model['reason'] : '-';
            },
            'format' => 'raw'
        ],

        [
            'header' => 'Answered',
            //'attribute' => 'l_answered',
            'value' => function ($model) {
                return $model['l_answered'] ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
            },
            'contentOptions' => ['class' => 'text-center'],
            'format' => 'html',
            'visible' => in_array($queueType, ['follow-up', 'processing', 'processing-all'])
        ],

        [
            'header' => 'Task Info',
            'value' => function ($model) {
                return '<small style="font-size: 10px">' . Lead::getTaskInfo2($model['id']) . '</small>';
            },
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left'],
            'visible' => $is_manager && in_array($queueType, ['follow-up', 'processing', 'processing-all']),
            'options' => ['style' => 'width:140px'],
        ],

        [
            'label' => 'Countdown',
            'visible' => ($div == Lead::DIV_GRID_IN_SNOOZE),
            'contentOptions' => ['style' => 'width: 115px;'],
            'value' => static function ($model) {
                return Lead::getSnoozeCountdown($model['id'], $model['snooze_for']);
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Rating',
            'visible' => !in_array($queueType, ['inbox']),
            'contentOptions' => ['style' => 'width: 90px;', 'class' => 'text-center'],
            'options' => ['class' => 'text-right'],
            'value' => static function ($model) {
                return Lead::getRating2($model['rating']);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'source_id',
            'label' => 'Market Info',
            'visible' => in_array($queueType, ['sold']) && !Yii::$app->user->identity->canRole('agent'),
            'value' => static function ($model) {
                return $model['name'];
            },
            'format' => 'raw'
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'contentOptions' => ['style' => 'width: 145px;'],
            'template' => $actionButtonTemplate,
            'buttons' => [
                'action' => function ($url, $model, $key) use ($queueType) {
                    $buttonsCnt = 0;
                    $buttons = '';
                    if (
                        in_array($queueType, ['inbox', 'follow-up']) ||
                        ($queueType === 'processing' &&
                            $model['status'] === Lead::STATUS_ON_HOLD)
                    ) {
                        $buttonsCnt++;
                        $buttons .= Html::a('Take', [
                            'lead/take',
                            'gid' => $model->gid
                        ], [
                            'class' => 'btn btn-primary btn-xs take-btn',
                            'data-pjax' => 0
                        ]);
                    }

                    if ($queueType != 'inbox') { // && $queueType != 'follow-up'
                        if (
                            Yii::$app->user->identity->getId() == $model['employee_id'] &&
                            $queueType = 'processing-all'
                        ) {
                            $queueType = 'processing';
                        }
                        $buttonsCnt++;
                        $buttons .= ' ' . Html::a('<i class="fa fa-search"></i>', ['lead/view', 'gid' => $model->gid], [
                            'class' => 'btn btn-info btn-xs',
                            'target' => '_blank',
                            'data-pjax' => 0,
                                'title' => 'View lead'
                        ]);
                    }

                    if (
                        Yii::$app->user->identity->getId() != $model['employee_id'] &&
                        in_array($model['status'], [Lead::STATUS_ON_HOLD, Lead::STATUS_PROCESSING])
                    ) {
                        $buttonsCnt++;
                        $buttons .= ' ' . Html::a('Take Over', [
                            'lead/take',
                            'gid' => $model->gid,
                            'over' => true
                        ], [
                            'class' => 'btn btn-xs take-processing-btn',
                                'data-pjax' => 0,
                            'data-status' => $model['status']
                        ]);
                    }

                    /*$limitedAgents = \common\models\SourcePermission::getEmployees([$model->flightRequest->source_id], false, ['limitedSaleAgent']);
                    $isSupervision = \common\models\SourcePermission::isSupervision(\common\models\SourcePermission::TEAM_NAME_SELLER, [$model->flightRequest->source_id]);
                    if ($isSupervision && !empty($limitedAgents)) {
                        $url = Url::to(['sales/assign', 'id' => $model->leads[0]->id]);
                        $buttons .= Html::a('Assign', '#', [
                            'class' => 'btn btn-sm assign-btn',
                            'data-url' => $url,
                            'data-pjax' => 0
                        ]);
                    }*/

                    if ($buttonsCnt > 2) {
                        $html = Html::tag('div', Html::button('Action', [
                            'class' => 'btn btn-sm dropdown-toggle',
                            'data-toggle' => 'dropdown',
                            'aria-expanded' => 'false'
                        ]) . Html::button('<span class="caret"></span>', [
                            'class' => 'btn btn-sm dropdown-toggle',
                            'data-toggle' => 'dropdown',
                            'aria-expanded' => 'false'
                        ]) . Html::tag('div', $buttons, [
                            'class' => 'dropdown-menu dropdown-btns'
                        ]), [
                            'class' => 'btn-group'
                        ]);
                    } else {
                        $html = $buttons;
                    }

                    return $html;
                }
            ]
        ]
    ]
])
?>
<?php \yii\widgets\Pjax::end();
