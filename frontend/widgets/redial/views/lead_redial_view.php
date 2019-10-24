<?php

use common\models\Lead;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var Lead $lead */

?>

<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-list"></i> Lead <?= $lead->id ?></h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $lead,
                'attributes' => [
                    'uid',
                    [
                        'attribute' => 'client.name',
                        'header' => 'Client name',
                        'format' => 'raw',
                        'value' => static function (Lead $model) {
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

                            return $clientName;
                        },
                        'options' => ['style' => 'width:160px'],
                    ],

                    [
                        'attribute' => 'client.phone',
                        'header' => 'Client Phones',
                        'format' => 'raw',
                        'value' => static function (Lead $model)  {
                            if ($model->client && $model->client->clientPhones) {
                                $str = '<i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', ArrayHelper::map($model->client->clientPhones, 'phone', 'phone'));
                            }
                            return $str ?? '-';
                        },
                        'options' => ['style' => 'width:180px'],
                    ],
                    [
                        'attribute' => 'status',
                        'value' => static function (Lead $model) {
                            return $model->getStatusName(true);
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'project_id',
                        'value' => static function (Lead $model) {
                            return $model->project ? $model->project->name : '-';
                        },
                    ],
                    [
                        'attribute' => 'source_id',
                        'value' => static function (Lead $model) {
                            return $model->source ? $model->source->name : '-';
                        },
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $lead,
                'attributes' => [
                    [
                        'attribute' => 'trip_type',
                        'value' => static function (Lead $model) {
                            return $model->getFlightTypeName();
                        },
                    ],
                    [
                        'attribute' => 'cabin',
                        'value' => static function (Lead $model) {
                            return $model->getCabinClassName();
                        },
                    ],
                    'offset_gmt',
                    [
                        'label' => 'Client time',
                        'format' => 'raw',
                        'value' => static function (Lead $model) {
                            return $model->getClientTime2();
                        },
                    ],
                    [
                        'attribute' => 'created',
                        'value' => static function (Lead $model) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Pending Time',
                        'value' => static function (Lead $model) {
                            $createdTS = strtotime($model->created);

                            $diffTime = time() - $createdTS;
                            $diffHours = (int)($diffTime / (60 * 60));

                            return ($diffHours > 3 && $diffHours < 73) ? $diffHours . ' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                        },
                        'options' => [
                            'style' => 'width:180px'
                        ],
                        'format' => 'raw',
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>
