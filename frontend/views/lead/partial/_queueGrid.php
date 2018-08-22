<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider ActiveDataProvider
 * @var $searchModel Lead
 * @var $div string
 */

use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use common\models\Lead;
use yii\helpers\Url;
use common\models\Quote;
use common\models\Employee;

$actionButtonTemplate = '{action}';
$queueType = Yii::$app->request->get('type');

?>
<?php \yii\widgets\Pjax::begin(['timeout' => 10000]); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => $template,
    'filterModel' => $searchModel,
    'rowOptions' => function ($model) {
        /**
         * @var $model Lead
         */
        if ($model->status === $model::STATUS_PROCESSING &&
            Yii::$app->user->identity->getId() == $model->employee_id) {
            return ['class' => 'highlighted'];
        }
        if (in_array($model->status, [$model::STATUS_ON_HOLD, $model::STATUS_BOOKED, $model::STATUS_FOLLOW_UP])) {
            $now = new \DateTime();
            $diff = $now->diff(new \DateTime($model->leadFlightSegments[0]->departure));
            $diffInSec = $diff->s + ($diff->i * 60) + ($diff->h * 3600) + ($diff->d * 86400) + ($diff->m * 30 * 86400) + ($diff->y * 12 * 30 * 86400);
            //if departure <= 7 days
            if ($diffInSec <= (7 * 24 * 60 * 60)) {
                return ['class' => 'success'];
            }
        }
    },
    'columns' => [
        [
            'attribute' => 'pending',
            'label' => 'Pending Time',
            'visible' => !in_array($queueType, ['sold', 'booked']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                return $model->getPendingAfterCreate();
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Pending in Booked',
            'visible' => in_array($queueType, ['booked']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                return $model->getPendingInLastStatus();
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Created Date',
            'visible' => !in_array($queueType, ['booked', 'sold']),
            'value' => 'created',
            'format' => ['date', 'php:m/d/y h:i a'],
        ],
        [
            'attribute' => 'id',
            'label' => in_array($queueType, ['booked', 'sold'])
                ? 'Lead ID / Sale ID (BO)' : 'Sale ID',
            'visible' => (
                Yii::$app->user->identity->role != 'agent' ||
                !in_array($queueType, ['inbox'])
            ),
            'value' => function ($model) use ($queueType) {
                /**
                 * @var $model Lead
                 */
                if (in_array($queueType, ['booked', 'sold'])) {
                    return sprintf('%d / %d', $model->id, $model->bo_flight_id);
                }

                return (!empty($model->id))
                    ? $model->id : '-';
            }
        ],
        [
            'label' => 'PNR',
            'visible' => in_array($queueType, ['booked', 'sold']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                $quote = $model->getAppliedAlternativeQuotes();
                return ($quote !== null && !empty($quote->record_locator))
                    ? $quote->record_locator : '-';
            }
        ],
        [
            'label' => 'Passengers',
            'visible' => in_array($queueType, ['booked', 'sold']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                $content = [];
                if (!empty($model->additionalInformationForm->passengers)) {
                    $content = $model->additionalInformationForm->passengers;
                }
                return implode('<br/>', $content);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'Client',
            'visible' => !in_array($queueType, ['booked']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                return $model->client->first_name;
            }
        ],
        [
            'label' => 'Client Email',
            'visible' => in_array($queueType, ['sold']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                if (!empty($model->client->clientEmails)) {
                    $emails = [];
                    foreach ($model->client->clientEmails as $email) {
                        $emails[] = $email->email;
                    }
                    return implode('<br/>', $emails);
                }
                return '---';
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Client Phone',
            'visible' => in_array($queueType, ['trash', 'sold']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                if (!empty($model->client->clientPhones)) {
                    $phones = [];
                    foreach ($model->client->clientPhones as $phone) {
                        $phones[] = $phone->phone;
                    }
                    return implode('<br/>', $phones);
                }
                return '---';
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'clientTime',
            'label' => 'Client Time',
            'visible' => !in_array($queueType, ['booked', 'sold']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                return $model->getClientTime();
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Destination',
            'visible' => in_array($queueType, ['booked', 'sold']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 * @var $segments \common\models\local\FlightSegment[]
                 */
                $quote = $model->getAppliedAlternativeQuotes();
                if ($quote !== null) {
                    $trips = $quote->getTrips();
                    $lastSegment = $trips[0]['segments'][count($trips[0]['segments']) - 1];
                    return sprintf('%s (%s)', $lastSegment['arrivalCity'], $lastSegment['arrivalAirport']);
                }
                return null;
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'Request Details',
            'visible' => !in_array($queueType, ['booked', 'sold']),
            'content' => function ($model) {
                /**
                 * @var $model Lead
                 */
                $return = '';
                $locations = [];
                foreach ($model->leadFlightSegments as $key => $segment) {
                    if ($model->trip_type != 'MC') {
                        if ($return == '') $return .= ' ' . $segment->departure . ' ';
                        if ($key == 0) $locations[] = $segment->origin;
                        $locations[] = $segment->destination;
                    } else {
                        $locations[] = ' ' . $segment->departure . ' ' . $segment->origin . '-' . $segment->destination;
                    }
                }
                if (
                    Yii::$app->user->identity->role != 'agent' ||
                    !in_array(Yii::$app->controller->action->id, ['inbox'])
                ) {
                    if ($model->trip_type != 'MC') {
                        $return .= implode('-', $locations);
                    } else {
                        $return .= implode('<br/>', $locations);
                    }
                }
                $return .= ' (<i class="fa fa-male"></i> x' . ($model->adults + $model->children + $model->infants) . ')';
                $return .= sprintf('<br/><strong>Cabin:</strong> %s', $model->getCabin($model->cabin));
                return $return;
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'Quotes ',
            'visible' => !in_array($queueType, ['booked', 'inbox', 'sold']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                return sprintf('Total: <strong>%d</strong> / Sent: <strong>%d</strong>',
                    count($model->getAltQuotes()),
                    $model->getSentCount()
                );
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'pending_in_trash',
            'label' => 'Pending in Trash',
            'visible' => in_array($queueType, ['trash']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                return $model->getPendingInLastStatus();
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
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                return ($model->employee !== null)
                    ? $model->employee->username : '-';
            }
        ],
        [
            'label' => 'Profit',
            'visible' => in_array($queueType, ['booked', 'sold']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                $profit = 0;
                $quote = $model->getAppliedAlternativeQuotes();
                if ($quote !== null) {
                    $price = $quote->quotePrice();
                    $profit = ($price['selling'] * Quote::SERVICE_FEE);
                }
                return sprintf('$%s', number_format($profit, 2));
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Date of Issue',
            'visible' => in_array($queueType, ['sold']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                return $model->updated;
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Date of Departure',
            'visible' => in_array($queueType, ['sold']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 * @var $segments \common\models\local\FlightSegment[]
                 */
                $quote = $model->getAppliedAlternativeQuotes();
                if ($quote !== null) {
                    $segments = [];
                    Quote::parseDump($quote->reservation_dump, false, $segments);
                    $lastSegment = $segments[0];
                    return $lastSegment->departureTime;
                }
                return null;
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Processing Status',
            'visible' => in_array($queueType, ['booked']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                $labelVTF = '<span class="label label-danger"><i class="fa fa-times"></i></span>';
                if (!empty($model->additionalInformationForm->vtf_processed)) {
                    $labelVTF = '<span class="label label-success"><i class="fa fa-check"></i></span>';
                }
                $labelTKT = '<span class="label label-danger"><i class="fa fa-times"></i></span>';
                if (!empty($model->additionalInformationForm->tkt_processed)) {
                    $labelTKT = '<span class="label label-success"><i class="fa fa-check"></i></span>';
                }
                $labelEXP = '<span class="label label-danger"><i class="fa fa-times"></i></span>';
                if (!empty($model->additionalInformationForm->exp_processed)) {
                    $labelEXP = '<span class="label label-success"><i class="fa fa-check"></i></span>';
                }
                return 'VTF: ' . $labelVTF . ' TKT: ' . $labelTKT . ' EXP: ' . $labelEXP;
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'last_activity',
            'label' => 'Last Activity',
            'visible' => !in_array($queueType, ['inbox', 'sold']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                return $model->getLastActivity();
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'reason',
            'label' => 'Reason',
            'visible' => !in_array($queueType, ['inbox', 'sold', 'booked']),
            'contentOptions' => ['style' => 'max-width: 250px;'],
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                $reason = $model->lastReason();
                return ($reason !== null)
                    ? $reason->reason : '-';
            }
        ],
        [
            'label' => 'Countdown',
            'visible' => ($div == Lead::DIV_GRID_IN_SNOOZE),
            'contentOptions' => ['style' => 'width: 115px;'],
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                return $model->getSnoozeCountdown();
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Rating',
            'visible' => !in_array($queueType, ['inbox']),
            'contentOptions' => ['style' => 'width: 115px;'],
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                return $model->getRating();
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'source_id',
            'label' => 'Market Info',
            'visible' => (
                in_array($queueType, ['sold']) &&
                (Yii::$app->user->identity->role != 'agent')
            ),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                return $model->source->name;
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Status',
            'visible' => !in_array($queueType, ['sold']),
            'value' => function ($model) {
                /**
                 * @var $model Lead
                 */
                return $model->getStatusLabel();
            },
            'format' => 'raw'
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'contentOptions' => ['style' => 'width: 145px;'],
            'template' => $actionButtonTemplate,
            'buttons' => [
                'action' => function ($url, $model, $key) use ($queueType) {
                    /**
                     * @var $model Lead
                     */
                    $buttons = '';
                    if (in_array($queueType, ['inbox', 'follow-up']) ||
                        ($queueType == 'processing' &&
                            $model->status === $model::STATUS_ON_HOLD)
                    ) {
                        $buttons .= Html::a('Take', Url::to([
                            'lead/take',
                            'id' => $model->id
                        ]), [
                            'class' => 'btn btn-action btn-sm take-btn',
                            'data-pjax' => 0
                        ]);
                    }

                    if ($queueType != 'inbox') {
                        if (Yii::$app->user->identity->getId() == $model->employee_id &&
                            $queueType = 'processing-all'
                        ) {
                            $queueType = 'processing';
                        }
                        $buttons .= Html::a('Open', Url::to(['lead/quote', 'type' => $queueType, 'id' => $model->id]), [
                            'class' => 'btn btn-action btn-sm',
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    }

                    if (Yii::$app->user->identity->getId() != $model->employee_id &&
                        in_array($model->status, [$model::STATUS_ON_HOLD, $model::STATUS_PROCESSING])
                    ) {
                        $buttons .= Html::a('Take Over', Url::to([
                            'lead/take',
                            'id' => $model->id,
                            'over' => true
                        ]), [
                            'class' => 'btn btn-action btn-sm take-processing-btn',
                            'data-pjax' => 0,
                            'data-status' => $model->status
                        ]);
                    }

                    /*$limitedAgents = \common\models\SourcePermission::getEmployees([$model->flightRequest->source_id], false, ['limitedSaleAgent']);
                    $isSupervision = \common\models\SourcePermission::isSupervision(\common\models\SourcePermission::TEAM_NAME_SELLER, [$model->flightRequest->source_id]);
                    if ($isSupervision && !empty($limitedAgents)) {
                        $url = Url::to(['sales/assign', 'id' => $model->leads[0]->id]);
                        $buttons .= Html::a('Assign', '#', [
                            'class' => 'btn btn-action btn-sm assign-btn',
                            'data-url' => $url,
                            'data-pjax' => 0
                        ]);
                    }*/

                    $html = Html::tag('div', Html::button('Action', [
                            'class' => 'btn btn-sm btn-action dropdown-toggle',
                            'data-toggle' => 'dropdown',
                            'aria-expanded' => 'false'
                        ]) . Html::button('<span class="caret"></span>', [
                            'class' => 'btn btn-action btn-sm dropdown-toggle',
                            'data-toggle' => 'dropdown',
                            'aria-expanded' => 'false'
                        ]) . Html::tag('div', $buttons, [
                            'class' => 'dropdown-menu dropdown-btns'
                        ]), [
                        'class' => 'btn-group'
                    ]);

                    return $html;
                }
            ]
        ]
    ]
])
?>
<?php \yii\widgets\Pjax::end(); ?>
