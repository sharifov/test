<?php

/**
 * @var array $trips
 * @var $segments []
 * @var $sourceHeight []
 * @var $sourceWeight []
 * @var array|null $defaultBaggage
 * @var bool $withBaggageForm
 */

use common\models\Airports;
use sales\forms\segment\SegmentBaggageForm;
use sales\forms\segment\SegmentTripForm;
use sales\helpers\quote\ImageHelper;
use sales\services\parsingDump\BaggageService;
use unclead\multipleinput\components\BaseColumn;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

$keyTripList = array_combine(array_keys($trips), array_keys($trips));
?>

<?php foreach ($trips as $keyTrip => $trip) : ?>
    <br /><h6 >Trip: <?php echo $keyTrip ?></h6>
    <?php foreach ($trip['segments'] as $key => $segment) : ?>
        <div class="row">
            <div class="col- border p-1">
                <strong>Segment <?php echo $key + 1 ?></strong><br />
                <?php
                    $segmentTripForm = new SegmentTripForm($segment['segmentIata']);
                    $segmentTripForm->segment_iata = $segment['segmentIata'];
                    $segmentTripForm->segment_trip_key = $keyTrip;
                ?>
                <?php $formSegmentTrip = ActiveForm::begin([
                        'id' => $segmentTripForm->formName(),
                        'enableClientValidation' => false,
                        'enableAjaxValidation' => false,
                        'options' => ['class' => 'segment_trip_forms'],
                        'fieldConfig' => [
                            'options' => [
                                'tag' => false,
                                'template' => '{input}',
                            ],
                        ],
                    ]) ?>

                    <?= $formSegmentTrip->field($segmentTripForm, 'segment_iata')->hiddenInput()->label(false) ?>
                    Trip: <?php echo $formSegmentTrip->field($segmentTripForm, 'segment_trip_key')
                        ->dropDownList($keyTripList, ['class' => '_'])
                        ->label(false) ?>

                <?php ActiveForm::end(); ?>

            </div>
            <div class="col-1 border p-1">
                <?php echo $segment['airlineName'] ?>
            </div>
            <div class="col-1 border p-1">
                <?php $airlineLogo = '//www.gstatic.com/flights/airline_logos/70px/' . $segment['carrier'] . '.png' ?>
                <?php if (ImageHelper::checkImageGstaticExist($airlineLogo)) : ?>
                    <span class="quote__vc-logo" style="margin-right: 2px;">
                        <img src="<?php echo $airlineLogo ?>" alt="<?= $segment['airlineName']?>" class="quote__airline-logo">
                    </span>
                <?php endif ?>
                <?php echo $segment['carrier'] ?>&nbsp;
                <?php echo $segment['flightNumber'] ?>
            </div>
            <div class="col-3 border p-1">
                <?php echo $segment['departureDateTime']->format('g:i A M d') ?>&nbsp;
                <?php echo ($departureAirport = Airports::findOne($segment['departureAirport'])) ?
                    $departureAirport->getCityName() : $segment['departureAirport'] ?>&nbsp;
                <b><?php echo $segment['departureAirport'] ?></b>
            </div>
            <div class="col-3 border p-1">
                <?php echo $segment['arrivalDateTime']->format('g:i A M d') ?>&nbsp;
                <?php echo ($arrivalAirport = Airports::findOne($segment['arrivalAirport'])) ?
                    $arrivalAirport->getCityName() : $segment['arrivalAirport'] ?>&nbsp;
                <b><?php echo $segment['arrivalAirport'] ?></b>
            </div>
        </div>

        <?php if ($withBaggageForm) : ?>

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
                    'validationUrl' => Url::to(['/flight/flight-quote/segment-baggage-validate', 'iata' => $segment['segmentIata']]),
                    'options' => [
                        'class' => 'segment_baggage_forms'
                    ]
                ]) ?>

                    <?php
                    if (isset($segment['baggage'])) {
                        $segmentBaggageForm->baggageData = $segment['baggage'];
                    } elseif ($defaultBaggage) {
                        $segmentBaggageForm->baggageData = $defaultBaggage;
                    }

                        $multipleInputId = 'multiple_w_' . $keyTrip . $key;

                        echo $formBaggage
                            ->field($segmentBaggageForm, 'baggageData')
                            ->label(false)
                            ->widget(
                                MultipleInput::class,
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
                                        'type'  => BaseColumn::TYPE_DROPDOWN,
                                        'options' => [
                                            'class' => 'form-control',
                                            'prompt' => '---',
                                        ],
                                        'headerOptions' => [
                                            'style' => 'width: 300px;',
                                        ],
                                        'items' => $sourceHeight,
                                    ],
                                    [
                                        'title' => 'Max Weight',
                                        'name' => 'weight',
                                        'type'  => BaseColumn::TYPE_DROPDOWN,
                                        'options' => [
                                            'class' => 'form-control',
                                            'prompt' => '---',
                                        ],
                                        'headerOptions' => [
                                            'style' => 'width: 300px;',
                                        ],
                                        'items' => $sourceWeight,
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
                                ]
                            )
                    ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <br />

        <?php
        $js = <<<JS
        var formBaggage = $('#{$formName}');
        
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
        <?php endif ?>
    <?php endforeach; ?>
<?php endforeach; ?>

<?php
$js = <<<JS
    $('.ui-autocomplete-input')
        .addClass('form-control')
        .focus(function () {
            $(this).autocomplete("search");
        });
JS;
$this->registerJs($js);
