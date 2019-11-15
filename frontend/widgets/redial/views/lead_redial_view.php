<?php

use common\models\Employee;
use common\models\Lead;
use sales\formatters\client\ClientTimeFormatter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var Lead $lead */
/** @var Employee $user */

$user = Yii::$app->user->identity;

?>

<div class="x_panel">
    <div class="x_content">
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $lead,
                'attributes' => [
                    [
                        'attribute' => 'id',
                        'visible' => !$user->isAgent()
                    ],
                    [
                        'attribute' => 'status',
                        'value' => static function (Lead $lead) {
                            return $lead->getStatusName(true);
                        },
                        'format' => 'html',
                        'visible' => $user->isAdmin(),
                    ],
                    [
                        'label' => 'Project',
                        'attribute' => 'project_id',
                        'value' => static function (Lead $lead) {
                            return $lead->project ? $lead->project->name : '-';
                        },
                    ],
                    [
                        'attribute' => 'created',
                        'value' => static function (Lead $lead) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($lead->created));
                        },
                        'format' => 'raw',
                        'visible' => !$user->isAgent(),
                    ],
                    [
                        'label' => 'Pending Time',
                        'value' => static function (Lead $lead) {
                            $createdTS = strtotime($lead->created);

                            $diffTime = time() - $createdTS;
                            $diffHours = (int)($diffTime / (60 * 60));

                            return ($diffHours > 3 && $diffHours < 73) ? $diffHours . ' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                        },
                        'options' => [
                            'style' => 'width:180px'
                        ],
                        'format' => 'raw',
                        'visible' => !$user->isAgent(),
                    ],

                ],
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $lead,
                'attributes' => [
                    [
                        'attribute' => 'client.name',
                        'header' => 'Client name',
                        'format' => 'raw',
                        'value' => static function (Lead $lead) {
                            if ($lead->client) {
                                $clientName = $lead->client->first_name . ' ' . $lead->client->last_name;
                                if ($clientName === 'Client Name') {
                                    $clientName = '- - - ';
                                } else {
                                    $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                                }
                            } else {
                                $clientName = '-';
                            }

                            return $clientName;
                        },
                        'options' => ['style' => 'width:160px'],
                    ],
                    [
                        'attribute' => 'client.phone',
                        'header' => 'Client Phones',
                        'format' => 'raw',
                        'value' => static function (Lead $lead) {
                            if ($lead->client && $lead->client->clientPhones) {
                                $str = '<i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', ArrayHelper::map($lead->client->clientPhones, 'phone', 'phone'));
                            }
                            return $str ?? '-';
                        },
                        'options' => ['style' => 'width:180px'],
                    ],
                    [
                        'label' => 'Client time',
                        'format' => 'raw',
                        'value' => static function (Lead $lead) {
                            return ClientTimeFormatter::dayHoursFormat($lead->getClientTime2(), $lead->offset_gmt);
                        },
                        'visible' => !$user->isAgent(),
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $lead,
                'attributes' => [
                    [
                        'attribute' => 'cabin',
                        'value' => static function (Lead $lead) {
                            return $lead->getCabinClassName();
                        },
                    ],
                    [
                        'attribute' => 'trip_type',
                        'value' => static function (Lead $lead) {
                            return $lead->getFlightTypeName();
                        },
                    ],
                    [
                        'label' => 'Depart',
                        'value' => static function (Lead $lead) {
                            foreach ($lead->leadFlightSegments as $sk => $segment) {
                                return date('d-M-Y', strtotime($segment->departure));
                            }
                            return '-';
                        },
                    ],
                    [
                        'label' => 'Pax',
                        'value' => static function (Lead $lead) {
                            return '<i class="fa fa-male"></i> <span title="adult">' . $lead->adults . '</span> / <span title="child">' . $lead->children . '</span> / <span title="infant">' . $lead->infants . '</span><br>';
                        },
                        'format' => 'raw',
                        'visible' => !$user->isAgent(),
                    ],
                    [
                        'label' => 'Segments',
                        'value' => static function (Lead $model) {
                            $segmentData = [];
                            foreach ($model->leadFlightSegments as $sk => $segment) {
                                $segmentData[] = ($sk + 1) . '. <code>' . Html::a($segment->origin . ' <i class="fa fa-long-arrow-right"></i> ' . $segment->destination, [
                                        'lead-flight-segment/view',
                                        'id' => $segment->id
                                    ], [
                                        'target' => '_blank',
                                        'data-pjax' => 0
                                    ]) . '</code>';
                            }
                            $segmentStr = implode('<br>', $segmentData);
                            return '' . $segmentStr . '';
                        },
                        'format' => 'raw',
                        'visible' => !$user->isAgent(),
                    ],

                ],
            ]) ?>
        </div>
    </div>
</div>
