<?php

/**
 * @var $lead Lead
 * @var $quote Quote
 * @var $prices QuotePrice[]
 * @var $project_id int
 */

use common\models\Currency;
use common\models\Lead;
use src\model\flightQuoteLabelList\service\FlightQuoteLabelListDictionary;
use src\model\flightQuoteLabelList\service\FlightQuoteLabelListService;
use src\services\parsingDump\lib\ParsingDump;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\models\Quote;
use common\models\QuotePrice;
use kartik\select2\Select2;
use common\models\Airline;
use common\models\Employee;

$enableGdsParsers = \Yii::$app->params['settings']['enable_gds_parsers_for_create_quote'];

$quotePriceUrl = Url::to(['quote/calc-price', 'quoteId' => $quote->id]);
$formID = sprintf('alt-quote-info-form-%d', $quote->id);

$paxCntTypes = [
    QuotePrice::PASSENGER_ADULT => $lead->adults,
    QuotePrice::PASSENGER_CHILD => $lead->children,
    QuotePrice::PASSENGER_INFANT => $lead->infants
];
?>

<?php $form = ActiveForm::begin([
    'action' => \yii\helpers\Url::to(['quote/save']),
    'errorCssClass' => '',
    'successCssClass' => '',
    'id' => $formID
]) ?>
<!------------- Add/Edit Alternative Quote Form ------------->
<div class="alternatives__item">

    <h6 id="head_reservation_result" style="display: none;">
        Imported reservation info
        <i class="fas fa-copy clipboard" data-clipboard-target="#box_reservation_result"></i>
    </h6>
    <div id="box_reservation_result" style="height: 1px; width: 1px; overflow: hidden;"></div>

    <div id="box_segments" ></div>

    <?php echo Html::textarea(
        'reservation_result',
        null,
        ['id' => 'reservation_result', 'style' => 'display:none;']
    )
?>

<?php $currencyLead = $quote->lead->leadPreferences->pref_currency ?? Currency::getDefaultCurrencyCode() ?>
<?php if ($currencyLead !== Currency::getDefaultCurrencyCode()) : ?>
<div class="quote_exclamation_currency">
    <i class="fa fa-exclamation-circle warning"></i> Lead Currency: <strong><?php echo $currencyLead ?></strong>
</div>
<?php endif ?>

    <div class="table-wrapper table-responsive ticket-details-block__table mb-20"
         id="alt-quote-fares-info-<?= $quote->id ?>">
        <?= $form->field($quote, 'id', [
            'options' => [
                'tag' => false,
            ],
            'template' => '{input}'
        ])->hiddenInput() ?>
        <?= $form->field($quote, 'lead_id', [
            'options' => [
                'tag' => false,
            ],
            'template' => '{input}'
        ])->hiddenInput() ?>
        <?= $form->field($quote, 'status', [
            'options' => [
                'tag' => false,
            ],
            'template' => '{input}'
        ])->hiddenInput() ?>
        <table class="table table-striped table-neutral" id="price-table">
            <thead>
            <tr>
                <th style="min-width: 100px;">Name</th>
                <th>Selling Price</th>
                <th>Net Price</th>
                <th>Fare</th>
                <th>Taxes</th>
                <th>Mark-up</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $applyBtn = [];
            foreach ($prices as $index => $price) : ?>
                <tr class="pax-type-<?= $price->passenger_type ?>" id="price-index-<?= $index ?>">
                    <td class="td-input">
                        <?= $price->passenger_type ?>
                        <?= $form->field($price, '[' . $index . ']id', [
                            'options' => [
                                'tag' => false,
                            ],
                            'template' => '{input}'
                        ])->hiddenInput() .
                        $form->field($price, '[' . $index . ']passenger_type', [
                            'options' => [
                                'tag' => false,
                            ],
                            'template' => '{input}'
                        ])->hiddenInput() .
                        $form->field($price, '[' . $index . ']service_fee', [
                            'options' => [
                                'tag' => false,
                            ],
                            'template' => '{input}'
                        ])->hiddenInput() .
                        $form->field($price, '[' . $index . ']oldParams', [
                            'options' => [
                                'tag' => false,
                            ],
                            'template' => '{input}'
                        ])->hiddenInput() .
                        $form->field($price, '[' . $index . ']extra_mark_up', [
                            'options' => [
                                'tag' => false,
                            ],
                            'template' => '{input}'
                        ])->hiddenInput() ?>
                    </td>
                    <td class="td-input">
                        <?= $form->field($price, '[' . $index . ']selling', [
                            'options' => [
                                'class' => 'input-group',
                            ],
                            'template' => '<span class="input-group-addon">$</span>{input}'
                        ])->textInput([
                            'class' => 'form-control alt-quote-price',
                            'maxlength' => 10,
                        ]) ?>
                    </td>
                    <td class="td-input">
                        <?= $form->field($price, '[' . $index . ']net', [
                            'options' => [
                                'class' => 'input-group',
                            ],
                            'template' => '<span class="input-group-addon">$</span>{input}'
                        ])->textInput([
                            'class' => 'form-control ',
                            'readonly' => true,
                            'maxlength' => 10,
                        ]) ?>
                    </td>
                    <td class="td-input">
                        <?= $form->field($price, '[' . $index . ']fare', [
                            'options' => [
                                'class' => 'input-group',
                            ],
                            'template' => '<span class="input-group-addon">$</span>{input}'
                        ])->textInput([
                            'class' => 'form-control alt-quote-price',
                            'maxlength' => 10,
                        ]) ?>
                    </td>
                    <td class="td-input">
                        <?= $form->field($price, '[' . $index . ']taxes', [
                            'options' => [
                                'class' => 'input-group',
                            ],
                            'template' => '<span class="input-group-addon">$</span>{input}'
                        ])->textInput([
                            'class' => 'form-control alt-quote-price',
                            'maxlength' => 10,
                        ]) ?>
                    </td>
                    <td class="td-input">
                        <?= $form->field($price, '[' . $index . ']mark_up', [
                            'options' => [
                                'class' => 'input-group',
                            ],
                            'template' => '<span class="input-group-addon">$</span>{input}'
                        ])->textInput([
                            'class' => 'form-control alt-quote-price mark-up',
                            'maxlength' => 10,
                        ]) ?>
                    </td>
                    <td class="td-input text-right">
                        <?php /* if (!in_array($price->passenger_type, $applyBtn) && $paxCntTypes[$price->passenger_type] > 1) {
                            $applyBtn[] = $price->passenger_type;
                            echo Html::button('<i class="fa fa-copy"></i>', [
                                'title' => '',
                                'data-toggle' => 'tooltip',
                                'data-placement' => 'top',
                                'data-original-title' => 'Clone Price for Pax Type ' . $price->passenger_type,
                                'class' => 'btn btn-primary btn-sm clone-alt-price-by-type',
                                'data-price-index' => $index,
                                'data-type' => $price->passenger_type
                            ]);
                        } */ ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <table class="table  table-neutral table-fixed">
                <tbody>
                <tr>
                    <th><label for="pnr">Record Locator</label></th>
                    <td class="td-input">
                        <?= $form->field($quote, 'record_locator', [
                            'options' => [
                                'tag' => false,
                            ],
                            'template' => '{input}'
                        ])->textInput() ?>
                    </td>
                    <th><label for="gds">GDS</label></th>
                    <td class="td-input">
                        <div class="select-wrap-label">
                            <?= $form->field($quote, 'gds', [
                                'options' => [
                                    'tag' => false,
                                ],
                                'template' => '{input}'
                            ])->dropDownList(ParsingDump::QUOTE_GDS_TYPE_MAP, [
                                'prompt' => '---',
                                'required' => 'required'
                            ]) ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><label for="pcc">PCC</label></th>
                    <td class="td-input">
                        <?= $form->field($quote, 'pcc', [
                            'options' => [
                                'tag' => false,
                            ],
                            'template' => '{input}'
                        ])->textInput() ?>
                    </td>
                    <th><label for="trip-type">Trip Type</label></th>
                    <td class="td-input">
                        <div class="select-wrap-label">
                            <?= $form->field($quote, 'trip_type', [
                                'options' => [
                                    'tag' => false,
                                ],
                                'template' => '{input}'
                            ])->dropDownList(Lead::getFlightTypeList()) ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><label for="cabin">Cabin</label></th>
                    <td class="td-input">
                        <div class="select-wrap-label">
                            <?= $form->field($quote, 'cabin', [
                                'options' => [
                                    'tag' => false,
                                ],
                                'template' => '{input}'
                            ])->dropDownList(Lead::getCabinList()) ?>
                        </div>
                    </td>
                    <th><label for="v-carrier">Validating Carrier</label></th>
                    <td class="td-input">
                        <?= $form->field($quote, 'main_airline_code', [
                            'options' => [
                                'tag' => false,
                            ],
                            'template' => '{input}'
                        ])->widget(Select2::class, [
                            'data' => Airline::getAirlinesMapping(true),
                            'options' => ['placeholder' => '---'],
                            'pluginOptions' => [
                                'allowClear' => false
                            ],
                        ]) ?>
                    </td>
                </tr>
                <tr>
                    <th><label for="<?= Html::getInputId($quote, 'check_payment') ?>">Check payment</label>
                    </th>
                    <td class="td-input custom-checkbox">
                        <?= $form->field($quote, 'check_payment', [
                            'options' => [
                                'tag' => false,
                            ],
                        ])->checkbox([
                                    'class' => 'alt-quote-price js-check-payment',
                                    'template' => '{input}'
                        ])->label(false); ?>
                        <label for="<?= Html::getInputId($quote, 'check_payment') ?>"></label>
                    </td>
                    <th class="td-input"><label for="fare-type">Fare Type</label></th>
                    <td class="td-input">
                        <div class="select-wrap-label">
                            <?= $form->field($quote, 'fare_type', [
                                'options' => [
                                    'tag' => false,
                                ],
                                'template' => '{input}'
                            ])->dropDownList(Quote::getFareType()) ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="td-input"><label for="fare-type">Quote label</label></th>
                    <td class="td-input" width="120">
                        <div class="select-wrap-label">
                            <?= Select2::widget([
                                'data' => FlightQuoteLabelListService::getListKeyDescription(FlightQuoteLabelListDictionary::MANUAL_CREATE_LABELS),
                                'name' => 'quote_label',
                                'size' => Select2::SIZE_SMALL,
                                'pluginOptions' => [
                                    'width' => '100%',
                                ],
                                'options' => [
                                    'placeholder' => '',
                                    'id' => 'quote_label',
                                    'multiple' => true,
                                ],
                            ]); ?>
                        </div>
                    </td>
                </tr>
                <?php if (!isset($project_id)) {
                    $project_id = $lead->project_id;
                } ?>
                <?php if (isset($project_id)) : ?>
                    <tr>
                        <th>Quote Creator</th>
                        <td class="td-input" colspan="3">
                            <div class="select-wrap-label">
                                <?= $form->field($quote, 'employee_id', [
                                    'options' => [
                                        'tag' => false,
                                    ],
                                    'template' => '{input}',
                                ])->dropDownList(Employee::getListByProject($project_id, false)) ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="col-sm-6">
            <ul class="nav nav-tabs" id="dumpTab">

                <?php if ($enableGdsParsers) : ?>
                    <li id="box-gds-tab" >
                        <?= Html::a(
                            'GDS Dump',
                            sprintf('#r-prepare_dump-%d', $quote->id),
                            ['data-toggle' => 'tab', 'class' => 'active']
                        ) ?>
                    </li>
                <?php else : ?>
                    <li class="base-tab" id="reservation-dump-tab">
                        <?= Html::a('Reservation Dump', sprintf('#r-dump-%d', $quote->id), ['data-toggle' => 'tab', 'class' => 'active']) ?>
                    </li>
                    <li class="base-tab">
                        <?= Html::a('Pricing', sprintf('#r-dump-pane-%d', $quote->id), ['data-toggle' => 'tab']) ?>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="tab-content">
                <?php if ($enableGdsParsers) : ?>
                    <div id="<?= sprintf('r-prepare_dump-%d', $quote->id) ?>" class="prepare_dump_box tab-pane fade in active show">
                        <?php echo Html::textarea(
                            'prepare_dump',
                            null,
                            ['id' => 'prepare_dump', 'rows' => 13, 'class' => 'form-control']
                        )
                        ?>
                    </div>
                <?php else : ?>
                    <div id="<?= sprintf('r-dump-%d', $quote->id) ?>" class="tab-pane fade in active show">
                        <?= $form->field($quote, 'reservation_dump', [
                            'options' => [
                                'tag' => false,
                            ],
                            'template' => '{input}'
                        ])->textarea([
                            'rows' => 5
                        ]) ?>
                    </div>
                    <div id="<?= sprintf('r-dump-pane-%d', $quote->id) ?>" class="tab-pane fade in">
                        <?= $form->field($quote, 'pricing_info', [
                            'options' => [
                                'tag' => false,
                            ],
                            'template' => '{input}'
                        ])->textarea([
                            'rows' => 5
                        ]) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="btn-wrapper">

        <?php if ($enableGdsParsers) :?>
            <?php if ($quote->isNewRecord) : ?>
                <?= Html::button('<i class="fa fa-recycle"></i> Import from GDS dump', [
                    'id' => 'prepare_dump_btn',
                    'class' => 'btn btn-warning',
                    'data-inner' => '<i class="fa fa-recycle"></i> Import from GDS dump',
                    'data-class' => 'btn btn-warning',
                    'width' => '172px',
                ]) ?>
                <?= Html::button('<i class="fa fa-check-circle"></i> Save from GDS dump', [
                    'id' => 'save_dump_btn',
                    'class' => 'btn btn-success',
                    'data-inner' => '<i class="fa fa-check-circle"></i> Save from GDS dump',
                    'data-class' => 'btn btn-success',
                    'width' => '168px',
                    'style' => 'display: none',
                ]) ?>
            <?php endif; ?>
        <?php else : ?>
            <?= Html::button('<i class="glyphicon glyphicon-remove-circle"></i> Cancel', [
                'id' => 'cancel-alt-quote',
                'class' => 'btn btn-danger base-btn'
            ]) ?>
            <?php
            $applied = Quote::findOne([
                'status' => Quote::STATUS_APPLIED,
                'lead_id' => $quote->lead_id
            ]);
            if (($quote->isNewRecord || $quote->status == $quote::STATUS_CREATED) && $applied === null) : ?>
                <?= Html::button('<i class="fa fa-save"></i> Save', [
                    'id' => 'save-alt-quote',
                    'class' => 'btn btn-primary base-btn'
                ]) ?>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</div>
<?php ActiveForm::end() ?>

<div class="modal fade in" id="modal-confirm-alt-itinerary" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reservation Dump</h4>
            </div>
            <div class="modal-body">
                <div class="diff-itinerary__content"></div>
                <div class="btn-wrapper modal-footer">
                    <?= Html::button('<i class="glyphicon glyphicon-remove-circle"></i> Cancel', [
                        'id' => 'cancel-confirm-quote',
                        'class' => 'btn btn-danger'
                    ]) ?>
                    <?= Html::button('<i class="fa fa-save"></i> Save', [
                        'id' => 'confirm-alt-quote',
                        'class' => 'btn btn-primary'
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS

    var leadId = '$lead->id';

    $('[data-toggle="tooltip"]').tooltip({html:true});
    
    $(document).on('keyup', '.alt-quote-price', function(event){
        var key = event.keyCode ? event.keyCode : event.which;
        validatePriceField($(this), key);
    });
    
    var changeAltQuotePrice = function(event) {
        $('.alt-quote-price').prop('readonly', true);
        $('.field-error').each(function() {
            $(this).removeClass('field-error');
        });
        $('.parent-error').removeClass('has-error');
        
        if ($(this).val().length === 0) {
            $(this).val(0);
        }
        
        let quotePriceUrl = '{$quotePriceUrl}';
        
        if ($(this).hasClass('js-check-payment')) {
            quotePriceUrl = quotePriceUrl + '&refresh=1'
        }
        
        var form = $('#$formID');
        $.ajax({
            type: 'post',
            url: quotePriceUrl,
            data: form.serialize(),
            success: function (data) {
                $.each(data, function( index, value ) {
                    $('#'+index).val(value);
                });
                $('.alt-quote-price').prop('readonly', false);
            },
            error: function (error) {
                console.log('Error: ' + error);
                $('.alt-quote-price').prop('readonly', false);
            }
        });
    };
    
    $(document).on('change', '.alt-quote-price', changeAltQuotePrice);
    $('#modal-lg').on('hidden.bs.modal', function (e) {
        $(document).off('change', '.alt-quote-price', changeAltQuotePrice);
    })

    /***  Cancel card  ***/
    $('#cancel-alt-quote').click(function (e) {
        e.preventDefault();
        var editBlock = $('#$formID');
        editBlock.parent().parent().removeClass('show');
        editBlock.parent().html('');
        $('#modal-lg').modal('hide');
        if ($(this).data('type') == 'search') {
            //$('#quick-search').modal('show');
        }
    });
    $('#cancel-confirm-quote').click(function (e) {
        e.preventDefault();
        $('#modal-confirm-alt-itinerary').modal('hide');
    });

    function addEditAltQuote(form, url)
    {
        $('.field-error').each(function() {
            $(this).removeClass('field-error');
        });
        $('.parent-error').removeClass('has-error');
        
        if($('#quote-gds').val() == '') {
            alert('Select GDS please');
            $('#quote-gds').focus();
            return false;
        }        
        if($('#quote-main_airline_code').val() == '') {
            alert('Select Validating Carrier please');
            $('#quote-main_airline_code').focus();
            return false;
        } 
                        
        $('#preloader').removeClass('hidden');
                
        $.ajax({
            url: url,
            type: form.attr("method"),
            data: form.serialize(),
            success: function (data) {
                var itineraryErr = false;
                $('#preloader').addClass('hidden');
                
                $.each(data, function( index, value ) {
                    $('#'+index).val(value);
                    if(index == 'quote-main_airline_code'){
                        $('#'+index).trigger('change');
                    }
                });
                if (data.success == false) {
                    $.each(data.errors, function( index, value ) {
                        $('#quote-'+index).addClass('field-error');
                        $('#quote-'+index).parent().addClass('has-error parent-error');
                        if (index == 'reservation_dump') {
                            itineraryErr = true;
                        }
                    });

                    $.each(data.errorsPrices, function( index, value ) {
                        $.each(value, function (idx, val){
                            $('#quoteprice-'+index+'-'+idx).addClass('field-error');
                            $('#quoteprice-'+index+'-'+idx).parent().addClass('has-error parent-error');
                        });

                    });

                    if (data.itinerary.length != 0) {
                        if (Object.keys(data.errors).length == 1 && itineraryErr) {
                            $('#modal-confirm-alt-itinerary .diff-itinerary__content').html('');
                            $.each(data.itinerary, function( index, value ) {
                                var divCh = $("<div/>").addClass("diff-itinerary__item").appendTo($('#modal-confirm-alt-itinerary .diff-itinerary__content'));
                                divCh.html('<div class="diff-itinerary__conf-number">'+ value +'</div>')
                            });
                            $('#modal-confirm-alt-itinerary').modal('show');
                        }
                    }
	            } else {
                    if (data.save == true) {
                         window.location.reload();
                    } else {
                        $('#modal-confirm-alt-itinerary .diff-itinerary__content').html('');
                        $.each(data.itinerary, function( index, value ) {
                            var divCh = $("<div/>").addClass("diff-itinerary__item").appendTo($('#modal-confirm-alt-itinerary .diff-itinerary__content'));
                            divCh.html('<div class="diff-itinerary__conf-number">'+ value +'</div>')
                        });
                        $('#modal-confirm-alt-itinerary').modal('show');
                    }
	            }
            },
            error: function (error) {
                console.log('Error: ' + error);
            }
        });
    }
    
    $('#save-alt-quote').click(function (e) {
        e.preventDefault();
        $('#alternativequote-status').val(1);
        var form = $('#$formID');
               
        if($('#quote-reservation_dump').val() == '') {
            alert('Insert Reservation dump please');
            $('#quote-reservation_dump').focus();
            return false;
        }        
        addEditAltQuote(form, form.attr("action"));
    });
    $('#confirm-alt-quote').click(function (e) {
        e.preventDefault();
        $('#modal-confirm-alt-itinerary').modal('hide');
        $('#alternativequote-status').val(1);
        var form = $('#$formID');
        addEditAltQuote(form, form.attr("action") + '?save=true');
    });
    
    $('.clone-alt-price-by-type').click(function(e) {
        e.preventDefault();
        $(this).blur();
        var priceIndex = $(this).data('price-index');
        var paxType = $(this).data('type');
        var list = {};
        $('#price-index-' + priceIndex + ' input').each(function() {
            var field = $(this).attr('id').split('-')[2];
            if ($.inArray(field, ['net', 'fare', 'mark_up', 'oldparams', 'selling', 'taxes']) != -1) {
                list[field] = $(this).val();
            }
        });
        $('.pax-type-' + paxType).each(function(index) {
            if (index != 0) {
                $(this).find('input').each(function() {
                    var field = $(this).attr('id').split('-')[2];
                    for (var key in list) {
                        if (field == key) {
                            $(this).val(list[field]);
                            break;
                        }
                    }
                });
            }
        });
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
    
    $('#prepare_dump_btn').click(function (e) {
        e.preventDefault(); 

        cleanErrors(); 
        cleanData();      

        $('#save_dump_btn').hide();                            
        let form = $('#$formID');

        loadingBtn($(this), true);        
        if (!checkPrepareDumpQuote()) {
            loadingBtn($(this), false); 
            return false;
        }

        $.ajax({
            url: '/quote/prepare-dump?lead_id=' + leadId,
            type: 'POST',
            data: form.serialize(),
            dataType: 'json'
        })
        .done(function(dataResponse) {
            loadingBtn($('#prepare_dump_btn'), false);

            if (dataResponse.status === 1) {
                if (dataResponse.validating_carrier.length) {
                   $('#quote-main_airline_code').val(dataResponse.validating_carrier).trigger('change');
                } 
                if (dataResponse.prices.length) {
                   $('#price-table tbody').html(dataResponse.prices); 
                }
                if (dataResponse.segments.length) {
                   $('#box_segments').html(dataResponse.segments); 
                }                
                if (dataResponse.reservation_dump.length) {                        
                    $('#reservation_result').val(dataResponse.reservation_dump.join("\\n"));
                    
                    var reservationDumpOut = dataResponse.reservation_dump.join("<br />");
                    $('#head_reservation_result').show();
                    $('#box_reservation_result').html(reservationDumpOut);
                }                    
                $('#save_dump_btn').show(500);                                                            
            } else {
                if (dataResponse.error.length) {                        
                    createNotifyByObject({
                        title: "Error",
                        type: "error",
                        text: dataResponse.error,
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
    });
    
    $('#save_dump_btn').click(function (e) {
        e.preventDefault();
        
        cleanErrors();        
         
        loadingBtn($(this), true);
        if (!checkPrepareDumpQuote()) {
            loadingBtn($(this), false);
            return false;
        }
                        
        $.ajax({
            url: '/quote/save-from-dump?lead_id=' + leadId,
            type: 'POST',
            data: $('#$formID, .segment_baggage_forms').serialize(),
            dataType: 'json'
        })
        .done(function(dataResponse) {
            loadingBtn($('#save_dump_btn'), false);
                
            if (dataResponse.status === 1) {                   
                createNotifyByObject({
                    title: 'Success',
                    type: 'success',
                    text: 'Quote created',
                    hide: true
                });
                window.location.reload();                                        
            } else {
            
                $.each(dataResponse.errorsPrices, function( index, value ) {
                    $.each(value, function (idx, val){                            
                        $('#quoteprice-'+index+'-'+idx).addClass('field-error');
                        $('#quoteprice-'+index+'-'+idx).parent().addClass('has-error parent-error');
                    });
                });
                $.each(dataResponse.errors, function( index, value ) {
                    $('#quote-'+index).addClass('field-error');
                    $('#quote-'+index).parent().addClass('has-error parent-error');
                    if (index == 'reservation_dump') {
                        itineraryErr = true;
                    }
                });
                                  
                if (dataResponse.errorMessage.length) {                        
                    createNotifyByObject({
                        title: "Error",
                        type: "error",
                        text: dataResponse.errorMessage,
                        hide: true
                    }); 
                }    
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
    
    function checkPrepareDumpQuote() {  
        let message = '';        
        if($('#prepare_dump').val() === '') {
            message = 'Insert dump please';
        } 
        if($('#quote-gds').val() === '') {
            message = 'Select GDS please';
        }         
        if (message !== '') {
            createNotifyByObject({title: "Error", type: "error",
                text: message, hide: true
            });
            return false;
        } 
        return true;   
    } 

    function cleanData() {   
        $('#head_reservation_result i').attr('class', 'fas fa-copy clipboard');
        $('#box_reservation_result').text('');
        $('#reservation_result').val('');
    }

    function cleanErrors() {    
        $('.field-error').each(function() {
            $(this).removeClass('field-error');
        });
        $('.parent-error').removeClass('has-error');
    }
    
    var clipboard = new ClipboardJS('.clipboard');
    clipboard.on('success', function(e) {
        $('.clipboard').attr('class', 'fas fa-check');
        e.clearSelection();
    });   
JS;
$this->registerJs($js);

$css = <<<CSS
    .nav-tabs li a.active {
        font-weight: 900;
    } 
    .clipboard {
        cursor: pointer;
    }
    #box_reservation_result {
        color: #7890a2;
        margin-bottom: 8px;
    }    
    .multiple-input-list th {
        border-bottom: 1px solid #dee2e6!important;
        font-weight: normal!important;
    } 
    .list-cell__button {
        padding: 12px 3px 3px 3px!important;
    }
    .multiple-input-list__btn:hover {
        -webkit-box-shadow: 1px 1px 2px 0 rgba(0,0,0,0.75);
        -moz-box-shadow: 1px 1px 2px 0 rgba(0,0,0,0.75);
        box-shadow: 1px 1px 2px 0 rgba(0,0,0,0.75);
    }
    .multiple-input-list__btn:hover .glyphicon {
        color: #e8e8e8;
    }    
    .multiple-input-list__item td {
        padding: 3px!important;
    }   
    .list-cell__button .js-input-remove {
        margin-top: 4px;
    }
CSS;
$this->registerCss($css);