<?php

/**
 * @var $segments []
 * @var $sourceHeight []
 * @var $sourceWeight []
 */

use common\models\Airport;
use sales\forms\segment\SegmentBaggageForm;
use sales\services\parsingDump\BaggageService;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii\jui\AutoComplete;
use \yii\widgets\ActiveForm;
use yii\helpers\Url;

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
            <?php echo Airport::findOne($segment['departureAirport']) ?
                Airport::findOne($segment['departureAirport'])->getCityName() : $segment['departureAirport'] ?>&nbsp;
            <?php echo $segment['departureAirport'] ?>
        </div>
        <div class="col border p-1">
            <?php echo $segment['arrivalDateTime']->format('g:i A M d') ?>&nbsp;
            <?php echo Airport::findOne($segment['arrivalAirport']) ?
                Airport::findOne($segment['arrivalAirport'])->getCityName() : $segment['arrivalAirport'] ?>&nbsp;
            <?php echo $segment['arrivalAirport'] ?>
        </div>
    </div>
    <div class="row">
        <div class="col-8">
            <?php
                $segmentBaggageForm = new SegmentBaggageForm($segment['segmentIata']);
                $formName = $segmentBaggageForm->formName();
            ?>

            <?php $formBaggage = ActiveForm::begin([
                'id' => $formName,
                'enableClientValidation' => false,
                'enableAjaxValidation' => true,
                'validationUrl' => Url::to(['quote/segment-baggage-validate', 'iata' => $segment['segmentIata']]),
                'options' => [
                    'class' => 'segment_baggage_forms'
                ]
            ]) ?>

                <?php
                    if (isset($segment['baggage'])) {
                        $segmentBaggageForm->baggageData = $segment['baggage'];
                    }

                    $multipleInputId = 'multiple_w_' . $key;

                    echo $formBaggage
                        ->field($segmentBaggageForm, 'baggageData')
                        ->label(false)
                        ->widget(MultipleInput::class,
                        [
                            'id' => $multipleInputId,
                            'max' => 10,
                            'theme' => MultipleInput::THEME_BS,
                            'enableError' => true,
                            'showGeneralError' => true,
                            'allowEmptyList' => true,
                            'columns' => [
                                [
                                    'title' => 'Baggage Type',
                                    'name' => 'type',
                                    'type'  => 'dropDownList',
                                    'items' => BaggageService::TYPE_LIST,
                                    'headerOptions' => [
                                        'style' => 'width: 120px;',
                                    ],
                                ],
                                [
                                    'title' => 'Pieces',
                                    'name' => 'piece',
                                    'enableError' => true,
                                    'headerOptions' => [
                                        'style' => 'width: 70px;',
                                    ],
                                    'defaultValue' => 1,
                                    'options' => [
                                        'maxlength' => 2,
                                    ],
                                ],
                                [
                                    'title' => 'Max Size',
                                    'name' => 'height',
                                    'type'  => AutoComplete::class,
                                    'options' => [
                                        'class' => 'form-control',
                                        'clientOptions' => [
                                            'source' => $sourceHeight,
                                            'appendTo' => '#modal-lg',
                                            'minLength' => 0,
                                        ],
                                    ],
                                ],
                                [
                                    'title' => 'Max Weight',
                                    'name' => 'weight',
                                    'type'  => AutoComplete::class,
                                    'options' => [
                                        'class' => 'form-control',
                                        'clientOptions' => [
                                            'source' => $sourceWeight,
                                            'appendTo' => '#modal-lg',
                                            'minLength' => 0,
                                        ],
                                    ],
                                ],
                                [
                                    'title' => 'Cost',
                                    'name' => 'price',
                                    'headerOptions' => [
                                        'style' => 'width: 90px;',
                                    ],
                                    'options' => [
                                        'maxlength' => 10,
                                    ],
                                ],
                                [
                                    'name' => 'segmentIata',
                                    'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,
                                    'defaultValue' => $segment['segmentIata'],
                                ],
                            ],
                        ])
                ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <br />

<?php
$js =<<<JS
    var formBaggage = $('#$formName');
    
    formBaggage.on('ajaxBeforeSend', function(event, jqXHR, settings) {        
        $('#$formName .form-control').removeClass('border-danger').prop('title', '');       
    }); 
    
    formBaggage.on('ajaxComplete', function(event, jqXHR, textStatus) {        
        $.each(jqXHR.responseJSON, function(keyEl, msgs) {
            
            var splitKeyEl = keyEl.split('-'); 
            splitKeyEl[0] = splitKeyEl[0] + '-baggagedata';
            var elementId = splitKeyEl.join('-');
            
            if (msgs.length) {
                var message = msgs.join(',');
                $('#' + elementId).addClass('border-danger').prop('title', message);
            }           
        });      
    });  
    
    $('#$multipleInputId').on('afterAddRow', function() {
        $('.ui-autocomplete-input')
            .addClass('form-control')
            .focus(function () {
                $(this).autocomplete("search");
            }); 
    });      
JS;
$this->registerJs($js);
?>

<?php endforeach; ?>

<?php
$js =<<<JS
    $('.ui-autocomplete-input')
        .addClass('form-control')
        .focus(function () {
            $(this).autocomplete("search");
        });
JS;
$this->registerJs($js);
?>
