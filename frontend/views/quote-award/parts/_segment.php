<?php

use common\models\Airline;
use src\helpers\lead\LeadHelper;
use kartik\select2\Select2;
use src\widgets\DateTimePicker;
use yii\web\JsExpression;

/**
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \modules\quoteAward\src\forms\AwardQuoteForm
 */

$select2Properties = [
    'theme' => Select2::THEME_KRAJEE,
    'options' => [
        'placeholder' => 'Select location ...',
        'multiple' => false,
    ],
    'pluginOptions' => [
        'width' => '100%',
        'allowClear' => true,
        'minimumInputLength' => 1,
        'language' => [
            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
        ],
        'ajax' => [
            'url' => ['/airport/get-list'],
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { return {term:params.term}; }'),
        ],
        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        'templateResult' => new JsExpression('formatRepo'),
        'templateSelection' => new JsExpression('function (data) { return data.selection || data.text;}'),
    ]
];
?>

<div>
    <!--    <h5 style="font-weight:bold">Trip</h5>-->
    <!---->

    <div style="margin-top: 5px">
        <?php $trips = $model->groupByTrip();
        if (count($model->segments)) : ?>
            <?php
            $segmentId = 1;
            $tripId = 1;
            $lastTripId = 1;
            foreach ($trips as $tripKey => $segments) : ?>
                <?php $lastTripId = $tripKey ?>
                <div class="card" style="margin-bottom: 5px">
                    <div class="card-header h6 bg-default">
                        <div class="d-flex justify-content-between">
                            <div>
                                <?= 'Trip ' . $tripId ?>
                            </div>
                            <div>
                                <?php if ($tripKey != 1) : ?>
                                    <a class="js-remove-trip-award text-danger"
                                       data-inner='<i class="fa fa-times" aria-hidden="true"></i> <span style="font-size: 14px">Remove</span>'
                                       data-id="<?= $tripKey ?>"
                                       data-class='js-remove-trip-award text-danger'
                                       href="javascript:void(0)"
                                       style="padding: 5px;"
                                    >
                                        <i class="fa fa-times" aria-hidden="true"></i> <span style="font-size: 14px">Remove</span>
                                    </a>
                                <?php endif; ?>
                            </div>

                        </div>

                    </div>
                    <div class="ard-body table-responsive">
                        <table class="table table-bordered table-award-segment" style="margin-bottom: 5px">
                            <thead>
                            <tr>
                                <th>Seg</th>
                                <th>Operated By</th>
                                <th>Origin</th>
                                <th>Destination</th>
                                <th>Departure</th>
                                <th>Arrival</th>
                                <th>Flight No</th>
                                <th>Cabin</th>
                                <th>Flight</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($segments as $index => $segment) : ?>
                                <tr id="segment-index-<?= $index ?>">
                                    <td style="width: 20px" class="text-center"><?= $segmentId ?></td>
                                    <td style="width: 250px" class="fix-select2-krajee"> <?= $form->field($segment, '[' . $index . ']operatedBy')->widget(Select2::class, [
                                            'theme' => Select2::THEME_KRAJEE,
                                            'data' => Airline::getAirlinesMapping(true),
                                            'options' => ['placeholder' => '---'],
                                            'pluginOptions' => [
                                                'allowClear' => false
                                            ],
                                        ])->label(false) ?></td>
                                    <td style="width: 250px" class="fix-select2-krajee">
                                        <?php
                                        $select2Properties['data'] = [];
                                        if (isset($segment['origin'])) {
                                            $select2Properties['data'] = [$segment['origin'] => $segment['originLabel']];
                                        }
                                        ?>
                                        <?= $form->field($segment, '[' . $index . ']origin')
                                            ->widget(Select2::class, $select2Properties)
                                            ->label(false) ?></td>
                                    <td style="width: 250px" class="fix-select2-krajee">
                                        <?php
                                        $select2Properties['data'] = [];
                                        if (isset($segment['destination'])) {
                                            $select2Properties['data'] = [$segment['destination'] => $segment['destinationLabel']];
                                        }
                                        ?>

                                        <?= $form->field($segment, '[' . $index . ']destination')
                                            ->widget(Select2::class, $select2Properties)->label(false) ?></td>
                                    <td style="width: 150px"> <?= $form->field($segment, '[' . $index . ']departure')
                                            ->widget(DateTimePicker::class, [
                                                'template' => '{input}',
                                                'clientOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'yyyy-mm-dd hh:ii',
                                                    'todayBtn' => true,
                                                    'startDate' => date('Y-m-d H:i', time()),
                                                    'minuteStep' => 1
                                                ],
                                            ])->label(false) ?>
                                    </td>

                                    <td style="width: 150px"> <?= $form->field($segment, '[' . $index . ']arrival')
                                            ->widget(DateTimePicker::class, [
                                                'template' => '{input}',
                                                'clientOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'yyyy-mm-dd hh:ii',
                                                    'todayBtn' => true,
                                                    'startDate' => date('Y-m-d H:i', time()),
                                                    'minuteStep' => 1
                                                ]
                                            ])->label(false) ?>
                                    </td>
                                    <td style="width: 100px"><?= $form->field($segment, '[' . $index . ']flight_number')->textInput()->label(false) ?></td>
                                    <td style="width: 120px"> <?= $form->field($segment, '[' . $index . ']cabin', [
                                        ])->dropDownList(LeadHelper::cabinList(), ['prompt' => '---'])->label(false) ?></td>
                                    <td style="width:115px">
                                        <?= $form->field($segment, '[' . $index . ']trip', ['template' => '{input}', 'options' => ['tag' => false]])->hiddenInput()->label(false) ?>
                                        <?= $form->field($segment, '[' . $index . ']flight')->dropDownList($model->getFlightList(), ['required' => 'required'])->label(false) ?></td>
                                    <td style="width:35px">
                                        <?php if ($index !== 0) : ?>
                                            <a class="btn btn-default js-remove-segment-award"
                                               data-inner='<i class="fa fa-trash" aria-hidden="true"></i>'
                                               data-id="<?= $index ?>"
                                               data-class='btn btn-default js-remove-segment-award'
                                               href="javascript:void(0)">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php $segmentId++ ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="d-flex" style="margin-left: 5px">
                            <a class="btn btn-import-gds js-dump-gds"
                               data-trip="<?= $tripKey ?>"
                               href="javascript:void(0)">
                                <i class="fa fa-plus" aria-hidden="true"></i> Import from GDS dump
                            </a>
                            <a class="btn btn-add-segment"
                               id="js-add-segment-award"
                               data-trip="<?= $tripKey ?>"
                               data-inner='<i class="fa fa-plus" aria-hidden="true"></i> Add Segment'
                               data-class='btn btn-add-segment'
                               href="javascript:void(0)">
                                <i class="fa fa-plus" aria-hidden="true"></i> Add Segment
                            </a>
                        </div>

                    </div>
                </div>
                <?php $tripId++ ?>
            <?php endforeach; ?>
            <div class="d-flex justify-content-center">
                <a class="btn btn-success"
                   id="js-add-segment-award"
                   data-trip="<?= $lastTripId + 1 ?>"
                   data-inner='<i class="fa fa-plus" aria-hidden="true"></i> Add Trip'
                   data-class='btn btn-success'
                   href="javascript:void(0)">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Trip
                </a>
            </div>

        <?php endif; ?>
    </div>
</div>

<style>
    .table-award-segment td, .table-award-segment th {
        padding: 5px !important;
    }

    .table-award-segment .select2-container--krajee .select2-selection--single .select2-selection__arrow {
        border-left-color: #e4e9ee !important;
        max-height: 28px;
    }

    .table-award-segment .select2-container--krajee .select2-selection--single .select2-selection__clear {
        right: 1.5rem;
        line-height: 23px;
        vertical-align: middle;
    }

    .table-award-segment th {
        font-weight: 400;
        font-size: 12px;
        line-height: 16px;
        color: #8895A7;
    }

    .bg-default {
        background-color: #F4F7FA !important;
        color: #474F58;
    }

    .btn-add-segment {
        color: #3A81BB;
    }

    .btn-import-gds {
        color: #F39C12;
    }
</style>