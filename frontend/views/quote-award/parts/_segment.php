<?php

use common\components\widgets\BaseForm;
use common\models\Airline;
use kartik\select2\Select2;
use kartik\typeahead\Typeahead;
use modules\quoteAward\src\forms\AwardQuoteForm;
use src\helpers\lead\LeadHelper;
use src\widgets\DateTimePicker;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * @var $form BaseForm
 * @var $model AwardQuoteForm
 */

$typeheadProperties = function (string $name) {
    return [
        'name' => '',
        'options' => [
            'autocomplete' => 'off',
            'placeholder' => 'Filter as you type ...'
        ],
        'pluginOptions' => ['highlight' => true],
        'pluginEvents' => [
            'typeahead:select' => new JsExpression("(ev, data) => {
                const input = document.getElementsByName('$name')[0];
                input.dataset.selection = data.selection;
                input.value = data.id;
            }"),
            'typeahead:change' => new JsExpression("(ev) => {
                const input = document.getElementsByName('$name')[0];
                if (!ev.target.value) {
                    input.dataset.selection = '';
                    input.value = '';
                } else {
                    ev.target.value = input.dataset.selection ?? '';
                }
            }")
        ],
        'dataset' => [
            [
                'remote' => [
                    'url' => Url::to(['airport/get-list']) . '?term=%term&raw=1',
                    'wildcard' => '%term'
                ],
                'templates' => [
                    'suggestion' => new JsExpression(
                        "Handlebars.compile('<p class=\"text-wrap\">{{text}}</p>')"
                    )
                ],
                'name' => 'id',
                'display' => 'selection',
                'limit' => 10
            ]
        ]
    ];
};

?>

<div>
    <!--    <h5 style="font-weight:bold">Trip</h5>-->
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
                                    <td style="width: 250px"> <?= $form->field($segment, '[' . $index . ']operatedBy')->widget(Select2::class, [
                                            'data' => Airline::getAirlinesMapping(true),
                                            'options' => ['placeholder' => '---'],
                                            'pluginOptions' => [
                                                'allowClear' => false
                                            ],
                                        ])->label(false) ?></td>
                                    <td style="width: 250px">
                                        <?= $form->field($segment, '[' . $index . ']origin')->simpleHidden() ?>
                                        <?= Typeahead::widget($typeheadProperties($segment->formName() . '[' . $index . '][origin]')) ?>
                                    </td>
                                    <td style="width: 250px">
                                        <?= $form->field($segment, '[' . $index . ']destination')->simpleHidden() ?>
                                        <?= Typeahead::widget($typeheadProperties($segment->formName() . '[' . $index . '][destination]')) ?>
                                    </td>
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

    .card .ard-body {
        overflow-x: visible;
    }

    .card .ard-body .twitter-typeahead .tt-suggestion {
        font-size: 13px;
    }

    .card .ard-body .twitter-typeahead .tt-highlight {
        color: #e15554;
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