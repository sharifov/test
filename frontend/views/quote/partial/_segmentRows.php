<?php

/**
 * @var $segments []
 * @var $baggage []
 */

use common\models\Airport;
use sales\forms\segment\SegmentBaggageForm;
use sales\services\parsingDump\BaggageService;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii\jui\AutoComplete;
use \yii\widgets\ActiveForm;

?>

<?php foreach ($segments as $key => $segment) : ?>
    <div class="row">
        <div class="col-1 border p-1">
            <strong>Segment <?php echo $key+1 ?></strong>
        </div>
        <div class="col-1 border p-1">
            <?php echo $segment['airlineName'] ?>
        </div>
        <div class="col-1 border p-1">
            <?php echo $segment['carrier'] ?>&nbsp;
            <?php echo $segment['flightNumber'] ?>
        </div>
        <div class="col-3 border p-1">
            <?php echo $segment['departureDateTime']->format('g:i A M d') ?>&nbsp;
            <?php echo Airport::findOne($segment['departureAirport'])->getCityName() ?>&nbsp;
            <?php echo $segment['departureAirport'] ?>
        </div>
        <div class="col border p-1">
            <?php echo $segment['arrivalDateTime']->format('g:i A M d') ?>&nbsp;
            <?php echo Airport::findOne($segment['arrivalAirport'])->getCityName() ?>&nbsp;
            <?php echo $segment['arrivalAirport'] ?>
        </div>
    </div>
    <div class="row">
        <div class="col-8">
            <?php $formBaggage = ActiveForm::begin([
                'id' => 'segmentBaggageForm_' . $segment['segmentIata'],
                'enableClientValidation' => true,
                'validateOnChange' => true,
                'options' => [
                    'class' => 'segment_baggage_forms'
                 ]
            ]) ?>

                <?php
                    $segmentBaggageForm = new SegmentBaggageForm($segment['segmentIata']);

                    if (isset($segment['baggage'])) {
                        $segmentBaggageForm->baggageData = $segment['baggage'];
                    }
                ?>

                <?php echo $formBaggage->field($segmentBaggageForm, 'baggageData')->widget(MultipleInput::class, [
                    'id' => 'multiple_w_' . $key,
                    'theme' => MultipleInput::THEME_BS,
                    'max' => 10,
                    'enableError' => true,
                    'showGeneralError' => true,
                    'allowEmptyList' => false,
                    'layoutConfig' => [
                        /*'wrapperClass' => 'col-12',
                        'labelClass' => 'col-md-2',
                        'errorClass' => 'col-md-12',
                        'buttonActionClass' => 'col-md-2',*/
                    ],
                    'columns' => [
                        [
                            'title' => 'Baggage Type',
                            'name' => 'type',
                            'type'  => 'dropDownList',
                            'items' => BaggageService::TYPE_LIST,
                            'headerOptions' => [
                                'style' => 'width: 120px;',
                            ]
                        ],
                        [
                            'title' => 'Pieces',
                            'name' => 'piece',
                            'headerOptions' => [
                                'style' => 'width: 70px;',
                            ]
                        ],
                        [
                            'title' => 'Max Size',
                            'name' => 'height',
                        ],
                        [
                            'title' => 'Max Weight',
                            'name' => 'weight',
                        ],
                        [
                            'title' => 'Cost',
                            'name' => 'price',
                            'headerOptions' => [
                                'style' => 'width: 90px;',
                            ]
                        ],
                        [
                            'name' => 'segmentIata',
                            'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,
                            'defaultValue' => $segment['segmentIata'],
                        ],
                    ],
                ])->label(false)  ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <br />
<?php endforeach; ?>
