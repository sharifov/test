<?php

use common\models\Airline;
use kartik\select2\Select2;
use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuotePaxPriceForm;
use modules\flight\src\useCases\voluntaryExchangeManualCreate\form\VoluntaryQuoteCreateForm;
use modules\product\src\entities\productQuote\ProductQuote;
use src\services\parsingDump\lib\ParsingDump;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;

/**
 * @var View $this
 * @var VoluntaryQuoteCreateForm $createQuoteForm
 * @var Flight $flight
 * @var FlightQuotePaxPrice[] $flightQuotePaxPrices
 * @var ProductQuote $originProductQuote
 * @var int $changeId
 * @var int $originQuoteId
 * @var int $caseId
 */

$paxCntTypes = [
    FlightPax::PAX_ADULT => $flight->fl_adults,
    FlightPax::PAX_CHILD => $flight->fl_children,
    FlightPax::PAX_INFANT => $flight->fl_infants
];
$pjaxId = 'pjax-container-vc';
?>

<?php Pjax::begin([
    'id' => $pjaxId,
    'enableReplaceState' => false,
    'enablePushState' => false,
    'timeout' => 2000,
]) ?>
<?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1], 'id' => 'add-quote-form', 'enableClientValidation' => true]) ?>

    <?php if (!empty($createQuoteForm->customerPackage)) : ?>
        <div class="row">
            <div class="col-md-12">
                <?php $customerPackage = unserialize($createQuoteForm->customerPackage, ['allowed_classes' => false]) ?>
                <h6>Customer Package Data:</h6>
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Processing Fee</th>
                        <th>Without Penalty</th>
                        <th>Without Price Difference</th>
                    </tr>
                    <tr>
                        <td><?php echo $createQuoteForm->serviceFeeAmount ?> <?php echo $createQuoteForm->serviceFeeCurrency . ' (per pax)' ?? '' ?></td>
                        <td><small><?php echo Yii::$app->formatter->asBooleanByLabel($customerPackage['withoutPenalty']) ?></small></td>
                        <td><small><?php echo Yii::$app->formatter->asBooleanByLabel($customerPackage['withoutPriceDiff']) ?></small></td>
                    </tr>
                </table>
            </div>
        </div>
    <?php endif ?>

    <div id="box_quote_pax_price">
        <?php echo $this->render('_flight_quote_pax_price', [
            'originProductQuote' => $originProductQuote,
            'createQuoteForm' => $createQuoteForm,
            'form' => $form,
        ]); ?>
    </div>

    <div class="row">
        <div class="col-md-12">

            <div id="box_segments"></div>
        </div>
    </div>
    <hr />
    <div class="row">
        <div class="col-md-12">

            <div class="_form-fields-wrapper">
                <div id="error_summary_box">
                    <?php echo $form->errorSummary($createQuoteForm)?>
                </div>

                <?php echo Html::hiddenInput('change_id', $changeId, ['id' => 'changeId'])?>
                <?php echo Html::hiddenInput('origin_quote_id', $originQuoteId, ['id' => 'originQuoteId'])?>
                <?php echo Html::hiddenInput('case_id', $changeId, ['id' => 'caseId'])?>
                <?php echo Html::hiddenInput('keyTripList', null, ['id' => 'keyTripList']) ?>

                <?php echo $form->field($createQuoteForm, 'flightId')->hiddenInput()->label(false) ?>
                <?php echo $form->field($createQuoteForm, 'customerPackage')->hiddenInput()->label(false) ?>
                <?php echo $form->field($createQuoteForm, 'serviceFeeAmount')->hiddenInput()->label(false) ?>
                <?php echo $form->field($createQuoteForm, 'serviceFeeCurrency')->hiddenInput()->label(false) ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($createQuoteForm, 'pcc', ['labelOptions' => ['class' => 'control-label', 'max' => 10]])->textInput() ?>

                                <?= $form->field($createQuoteForm, 'gds', ['labelOptions' => ['class' => 'control-label']])->dropDownList(ParsingDump::QUOTE_GDS_TYPE_MAP, ['prompt' => '---']) ?>

                                <?= $form->field($createQuoteForm, 'cabin', ['labelOptions' => ['class' => 'control-label']])->dropDownList(Flight::getCabinClassList(), ['prompt' => '---']) ?>

                                <?= $form->field($createQuoteForm, 'fareType', ['labelOptions' => ['class' => 'control-label']])->dropDownList(FlightQuote::getFareTypeList(), ['prompt' => '---']) ?>

                                <?= $form->field($createQuoteForm, 'quoteCreator')->hiddenInput()->label(false) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($createQuoteForm, 'recordLocator', ['labelOptions' => ['class' => 'control-label', 'max' => 8]])->textInput() ?>

                                <?= $form->field($createQuoteForm, 'tripType', ['labelOptions' => ['class' => 'control-label']])->dropDownList(Flight::getTripTypeList(), ['prompt' => '---']) ?>

                                <?= $form->field($createQuoteForm, 'validatingCarrier', [
                                    'options' => [
                                        'tag' => false,
                                    ],
                                    'template' => '<div class="form-group required">{label}{input}</div>',
                                    'labelOptions' => ['class' => 'control-label']
                                ])->widget(Select2::class, [
                                    'data' => Airline::getAirlinesMapping(true),
                                    'options' => ['placeholder' => 'Select'],
                                    'pluginOptions' => [
                                        'allowClear' => false
                                    ],
                                    'size' => Select2::SIZE_SMALL
                                ])->label(true) ?>

                                <?= $form->field($createQuoteForm, 'expirationDate', [
                                    'labelOptions' => ['class' => 'control-label']
                                ])->widget(DatePicker::class, [
                                    'inline' => false,
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd',
                                    ],
                                    'options' => [
                                        'autocomplete' => 'off',
                                        'placeholder' => 'Choose Date',
                                    ],
                                ]) ?>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?= $form->field($createQuoteForm, 'reservationDump', ['labelOptions' => ['class' => 'control-label']])->textarea(['rows' => 7]) ?>
                            </div>

                        </div>

                        <?= Html::hiddenInput('baggage_data', null, ['id' => 'baggage_data']) ?>
                        <?= Html::hiddenInput('segment_trip_data', null, ['id' => 'segment_trip_data']) ?>

                        <div class="row">
                            <div class="col-md-12 text-center">
                                <?= Html::submitButton('<i class="fa fa-recycle"></i> Import from GDS dump', [
                                    'id' => 'prepare_dump_btn',
                                    'class' => 'btn btn-warning',
                                    'data-inner' => '<i class="fa fa-recycle"></i> Import from GDS dump',
                                    'data-class' => 'btn btn-warning',
                                    'width' => '172px',
                                ]) ?>
                                <?= Html::submitButton('<i class="fa fa-check-circle"></i> Save from GDS dump', [
                                    'id' => 'save_dump_btn',
                                    'class' => 'btn btn-success',
                                    'data-inner' => '<i class="fa fa-check-circle"></i> Save from GDS dump',
                                    'data-class' => 'btn btn-success',
                                    'width' => '168px',
                                    'style' => 'display: none',
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php ActiveForm::end(); ?>

<?php Pjax::end() ?>
    <?php
    $urlPrepareDump = \yii\helpers\Url::to([
        '/flight/flight-quote/ajax-prepare-dump',
        'flight_id' => $flight->getId(),
        'change_id' => $changeId,
    ]);
    $urlSave = \yii\helpers\Url::to(['/flight/flight-quote/save-voluntary-quote', 'flight_id' => $flight->getId()]);
    $urlRefreshPrice = \yii\helpers\Url::to(
        [
            '/flight/flight-quote/refresh-voluntary-price',
            'flight_id' => $flight->getId(),
            'origin_quote_id' => $originQuoteId,
        ]
    );

    $js = <<<JS
    var addVoluntaryQuoteForm = $('#add-quote-form');
    var lastPriceIdEl = '';

    addVoluntaryQuoteForm.on('click', '.alt-quote-price', function (event) {
        lastPriceIdEl = $(this).attr('id');
    });

    addVoluntaryQuoteForm.on('change', '.alt-quote-price', function (event) {
        if (typeof $(this).attr('min') !== 'undefined') {
            let min = parseFloat($(this).attr('min'));
            let val = parseFloat($(this).val());
            if (val < min) {
                $(this).val(min);
                return false;
            }
        }

        $('#box_loading').html('<span class="spinner-border spinner-border-sm"></span>');
        $('.alt-quote-price').prop('readonly', true);
        
        $.ajax({
            url: '{$urlRefreshPrice}',
            type: 'POST',
            data: addVoluntaryQuoteForm.serialize(),
            dataType: 'json'
        })
        .done(function(dataResponse) {
            if (dataResponse.status === 1) {
                $('#box_quote_pax_price').html(dataResponse.data);
            } else {
                if (dataResponse.message.length) {
                    createNotifyByObject({
                        title: "Error",
                        type: "error",
                        text: dataResponse.message,
                        hide: true
                    }); 
                }
            }
        })
        .fail(function(error) {
            console.log(error);
        })
        .always(function() {
            $('#box_loading').html('');
            $('.alt-quote-price').prop('readonly', false);
            if (lastPriceIdEl.length) {
                $('#' + lastPriceIdEl).focus();
            }
        });
    });

    $(document).on('beforeSubmit', '#add-quote-form', function(event) {
        let baggageData = $('.segment_baggage_forms').serialize();
        $('#baggage_data').val(baggageData);
        $('#segment_trip_data').val($('.segment_trip_forms').serialize());
    });
    
    addVoluntaryQuoteForm.on('click', '#save_dump_btn', function () {

        $('#error_summary_box').html('');
        let baggageData = $('.segment_baggage_forms').serialize();
        $('#baggage_data').val(baggageData);
        $('#segment_trip_data').val($('.segment_trip_forms').serialize());
        
        loadingBtn($(this), true);
        
        $.ajax({
            url: '{$urlSave}',
            type: 'POST',
            data: addVoluntaryQuoteForm.serialize(),
            dataType: 'json'
        })
        .done(function(dataResponse) {
            
            if (dataResponse.status === 1) {
                if ($('#pjax-case-orders').length) {
                    $.pjax.reload({container: '#pjax-case-orders', push: false, replace: false, timeout: 10000, async: false});
                }
                
                if ($('#modal-lg').length) {
                    let modal = $('#modal-lg');
                    modal.modal('hide');
                }
                if (dataResponse.message.length) {
                    createNotifyByObject({
                        title: "Quote saved",
                        type: "success",
                        text: dataResponse.message,
                        hide: true
                    }); 
                }
                $('#box_segments').html('');
                $('#save_dump_btn').hide(500);
            } else {
                if (dataResponse.message.length) {
                    createNotifyByObject({
                        title: "Error",
                        type: "error",
                        text: dataResponse.message,
                        hide: true
                    }); 
                }    
                $('#save_dump_btn').show(500);
            }
        })
        .fail(function(error) {
            loadingBtn($('#save_dump_btn'), false);
            console.log(error);
        })
        .always(function() {
            setTimeout(loadingBtn, 4000, $('#save_dump_btn'), false);
        });
    });

    addVoluntaryQuoteForm.on('click', '#prepare_dump_btn', function () {

        $('#error_summary_box').html('');
        let dump = $('#reservationdump').val();
        let gds = $('#gds').val();
        let flight_id = $('#flightId').val();
        let tripType = $('#triptype').val();

        if (dump.length && gds.length) {
            loadingBtn($(this), true);

            $.ajax({
                url: '{$urlPrepareDump}',
                type: 'POST',
                data: {reservationDump: dump, gds: gds, flight_id: flight_id, tripType: tripType},
                dataType: 'json'
            })
            .done(function(dataResponse) {
                loadingBtn($('#prepare_dump_btn'), false);
                
                if (dataResponse.status === 1) {
                    if (dataResponse.segments.length) {
                       $('#box_segments').html(dataResponse.segments); 
                    }
                    $('#keyTripList').val(dataResponse.key_trip_list);
                    
                    $('#save_dump_btn').show(500);
                } else {
                    if (dataResponse.message.length) {
                        createNotifyByObject({
                            title: "Error",
                            type: "error",
                            text: dataResponse.message,
                            hide: true
                        }); 
                    }    
                    $('#save_dump_btn').hide(500);
                }
            })
            .fail(function(error) {
                loadingBtn($('#prepare_dump_btn'), false);
                console.log(error);
            })
            .always(function() {
                setTimeout(loadingBtn, 4000, $('#prepare_dump_btn'), false);
            });
        }
    });

    function loadingBtn(btnObj, loading)
    {
        if (loading === true) {
            btnObj.removeClass()
                .addClass('btn btn-default')
                .html('<span class="spinner-border spinner-border-sm"></span> Loading')
                .prop("disabled", true);
        } else {
            let origClass = btnObj.data('class');
            let origInner = btnObj.data('inner');
            btnObj.removeClass()
                .addClass(origClass)
                .html(origInner)
                .prop("disabled", false);
        }  
    }
JS;
    $this->registerJs($js);
    ?>
