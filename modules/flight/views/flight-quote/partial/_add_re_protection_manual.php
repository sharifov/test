<?php

use common\models\Airline;
use common\models\Employee;
use common\models\Lead;
use kartik\select2\Select2;
use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuoteCreateForm;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuotePaxPriceForm;
use modules\flight\src\useCases\reProtectionQuoteManualCreate\form\ReProtectionQuoteCreateForm;
use modules\product\src\entities\productQuote\ProductQuote;
use src\services\parsingDump\lib\ParsingDump;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;
use yii\jui\AutoComplete;

/**
 * @var View $this
 * @var ReProtectionQuoteCreateForm $createQuoteForm
 * @var Flight $flight
 * @var FlightQuotePaxPrice[] $flightQuotePaxPrices
 * @var ProductQuote $originProductQuote
 * @var int $changeId
 */

$paxCntTypes = [
    FlightPax::PAX_ADULT => $flight->fl_adults,
    FlightPax::PAX_CHILD => $flight->fl_children,
    FlightPax::PAX_INFANT => $flight->fl_infants
];
$pjaxId = 'pjax-container-prices';

?>

    <br />
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-neutral">
                <thead>
                <tr class="text-center">
                    <th>Pax Type</th>
                    <th>X</th>
                    <th>Fare</th>
                    <th>Taxes</th>
                    <th>Mark-up</th>
                    <th>SFP, %</th>
                    <th>Selling Price, <?= $originProductQuote->pq_origin_currency ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                /** @var FlightQuotePaxPrice $paxPrice */
                foreach ($flightQuotePaxPrices as $index => $paxPrice) : ?>
                    <tr class="text-center pax-type-<?= FlightPax::getPaxTypeById($paxPrice->qpp_flight_pax_code_id) ?>" id="price-index-<?= $index ?>">
                        <td class="td-input">
                            <?= FlightPax::getPaxTypeById($paxPrice->qpp_flight_pax_code_id) ?>
                        </td>
                        <td><?= $paxPrice->qpp_cnt ?></td>
                        <td class="td-input">
                            <?= $paxPrice->qpp_fare ?>
                        </td>
                        <td class="td-input">
                            <?= $paxPrice->qpp_tax ?>
                        </td>
                        <td class="td-input">
                            <?= $paxPrice->qpp_agent_mark_up ?>
                        </td>
                        <td>
                            <?= $originProductQuote->pq_service_fee_percent ?>
                        </td>
                        <td class="text-right">
                            <?= ($paxPrice->qpp_fare + $paxPrice->qpp_tax + $paxPrice->qpp_agent_mark_up + $paxPrice->qpp_system_mark_up) * $paxPrice->qpp_cnt ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <hr />
    <div class="row">
        <div class="col-md-12">
            <div id="box_segments"></div>
        </div>
    </div>
    <hr />
    <div class="row">
        <div class="col-md-12">
            <?php Pjax::begin([
                    'id' => $pjaxId,
                    'enableReplaceState' => false,
                    'enablePushState' => false,
                    'timeout' => 2000,
            ]) ?>
            <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1], 'id' => 'add-quote-form', 'enableClientValidation' => true]) ?>

            <div class="_form-fields-wrapper">
                <div id="error_summary_box">
                    <?php echo $form->errorSummary($createQuoteForm)?>
                </div>

                <?php // echo Html::hiddenInput('flightId', $flight->fl_id, ['id' => 'flightId'])?>
                <?= Html::hiddenInput('keyTripList', null, ['id' => 'keyTripList']) ?>

                <?php echo $form->field($createQuoteForm, 'flightId')->hiddenInput()->label(false) ?>

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

            <?php ActiveForm::end(); ?>

            <?php Pjax::end() ?>
        </div>
    </div>

    <?php
    $urlPrepareDump = \yii\helpers\Url::to([
        '/flight/flight-quote/ajax-prepare-dump',
        'flight_id' => $flight->getId(),
        'change_id' => $changeId,
    ]);
    $urlSave = \yii\helpers\Url::to([
        '/flight/flight-quote/ajax-save-re-protection',
        'flight_id' => $flight->getId(),
        'change_id' => $changeId,
    ]);
    $js = <<<JS
    var addRPQuoteForm = $('#add-quote-form');

    $(document).on('beforeSubmit', '#add-quote-form', function(event) {
        let baggageData = $('.segment_baggage_forms').serialize();
        $('#baggage_data').val(baggageData);        
        $('#segment_trip_data').val($('.segment_trip_forms').serialize());
    });
    
    addRPQuoteForm.on('click', '#save_dump_btn', function () {

        $('#error_summary_box').html('');
        let baggageData = $('.segment_baggage_forms').serialize();
        $('#baggage_data').val(baggageData);
        $('#segment_trip_data').val($('.segment_trip_forms').serialize());
        
        loadingBtn($(this), true);
        
        $.ajax({
            url: '{$urlSave}',
            type: 'POST',
            data: addRPQuoteForm.serialize(),
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

    addRPQuoteForm.on('click', '#prepare_dump_btn', function () {

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
