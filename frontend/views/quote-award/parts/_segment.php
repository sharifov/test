<?php

use modules\quoteAward\src\models\SegmentAwardQuoteItem;
use src\widgets\DateTimePicker;
use kartik\select2\Select2;
use yii\web\JsExpression;

/**
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \modules\quoteAward\src\forms\AwardQuoteForm
 */

$select2Properties = [
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
    <h5>Trip</h5>

    <a class="btn btn-success"
       id="js-add-segment-award"
       data-inner='<i class="fa fa-plus" aria-hidden="true"></i> Add Segment'
       data-class='btn btn-success'
       href="javascript:void(0)">
        <i class="fa fa-plus" aria-hidden="true"></i>Add Segment
    </a>
    <div style="margin-top: 15px">
        <?php if (count($model->segments)) : ?>
            <table class="table table-neutral" id="price-table">
                <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Origin</th>
                    <th>Destination</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Flight Number</th>
                    <th>Trip</th>
                    <th>Flight</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($model->segments as $index => $segment) : ?>
                    <tr id="segment-index-<?= $index ?>">
                        <td style="width:35px">
                            <?php if ($index !== 0) : ?>
                                <a class="btn btn-danger js-remove-segment-award"
                                   data-inner='<i class="glyphicon glyphicon-remove " aria-hidden="true"></i>'
                                   data-id="<?= $index ?>"
                                   data-class='btn btn-danger js-remove-segment-award'
                                   href="javascript:void(0)">
                                    <i class="glyphicon glyphicon-remove" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td style="width: 100px"><?= 'Segment ' . ($index + 1) ?></td>
                        <td> <?= $form->field($segment, '[' . $index . ']origin')->widget(Select2::class, $select2Properties)->label(false) ?></td>
                        <td> <?= $form->field($segment, '[' . $index . ']destination')->widget(Select2::class, $select2Properties)->label(false) ?></td>
                        <td> <?= $form->field($segment, '[' . $index . ']departure')
                                ->widget(
                                    DateTimePicker::class,
                                    [
                                        'clientOptions' => [
                                            'autoclose' => true,
                                            'format' => 'yyyy-mm-dd hh:ii',
                                            'todayBtn' => true

                                        ]
                                    ]
                                )->label(false) ?>
                        </td>

                        <td> <?= $form->field($segment, '[' . $index . ']arrival')
                                ->widget(
                                    DateTimePicker::class,
                                    [
                                        'clientOptions' => [
                                            'autoclose' => true,
                                            'format' => 'yyyy-mm-dd hh:ii',
                                            'todayBtn' => true

                                        ]
                                    ]
                                )->label(false) ?>
                        </td>
                        <td><?= $form->field($segment, '[' . $index . ']flight_number')->textInput()->label(false) ?></td>
                        <td style="width:105px"><?= $form->field($segment, '[' . $index . ']trip')->dropDownList(SegmentAwardQuoteItem::getTrips(), ['required' => 'required'])->label(false) ?></td>
                        <td style="width:115px"><?= $form->field($segment, '[' . $index . ']flight')->dropDownList($model->getFlightList(), ['required' => 'required'])->label(false) ?></td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>