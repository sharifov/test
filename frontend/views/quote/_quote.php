<?php
/**
 * @var $lead Lead
 * @var $quote Quote
 * @var $prices QuotePrice[]
 * @var $project_id int
 */

use common\models\Lead;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\models\Quote;
use common\models\QuotePrice;
use kartik\select2\Select2;
use common\models\Airline;
use common\models\Employee;

$quotePriceUrl = \yii\helpers\Url::to(['quote/calc-price', 'quoteId' => $quote->id]);
$formID = sprintf('alt-quote-info-form-%d', $quote->id);

$paxCntTypes = [
    QuotePrice::PASSENGER_ADULT => $lead->adults,
    QuotePrice::PASSENGER_CHILD => $lead->children,
    QuotePrice::PASSENGER_INFANT => $lead->infants
];

$js = <<<JS
    $('[data-toggle="tooltip"]').tooltip();

    $('.alt-quote-price').keyup(function (event) {
        var key = event.keyCode ? event.keyCode : event.which;
        validatePriceField($(this), key);
    });
    $('.alt-quote-price').change(function (event) {
        if ($(this).val().length == 0) {
            $(this).val(0);
        }
        var form = $('#$formID');
        $.ajax({
            type: 'post',
            url: '$quotePriceUrl',
            data: form.serialize(),
            success: function (data) {
                $.each(data, function( index, value ) {
                    $('#'+index).val(value);
                });
            },
            error: function (error) {
                console.log('Error: ' + error);
            }
        });
    });
    
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
    
    $('#quote-gds').change(function (e) {
        e.preventDefault();
        
        let gds = $(this).val();
        
        if (gds === 'S' || gds === 'W') {
            $('#prepare_dump_btn').show(1000);
            $('#box-gds-tab').show();             
            $('#dumpTab #box-gds-tab a').tab('show');       
        } else {
            $('#prepare_dump_btn').hide(1000);
            $('#box-gds-tab').hide();             
            $('#dumpTab #reservation-dump-tab a').tab('show');       
        }
    });
    
    $('#prepare_dump_btn').click(function (e) {
        e.preventDefault();
        $('#alternativequote-status').val(1);
        var form = $('#$formID');        
        if($('#prepare_dump').val() == '') {
            alert('Insert Reservation dump please');
            $('#quote-reservation_dump').focus();
            return false;
        }        
        prepareDumpQuote(form, '/quote/prepare-dump');
    }); 
    
    function prepareDumpQuote(form, url)
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
                        
        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            beforeSend: function () {                    
                $('#preloader').removeClass('hidden');
            },
            success: function (dataResponse) {               
                $('#preloader').addClass('hidden');
                
                console.log(dataResponse); // TODO:: for debug
                
                if (dataResponse.status === 1) {
                
                   /* $.each(dataResponse., function( index, value ) {
                        $('#'+index).val(value);
                        if(index == 'quote-main_airline_code'){
                            $('#'+index).trigger('change');
                        }
                    });*/
                    
                   //
                    if (dataResponse.validating_carrier.length) {
                       $('#quote-main_airline_code').val(dataResponse.validating_carrier).trigger('change');
                    }
                    if (dataResponse.prices.length) {
                       $('#price-table tbody').html(dataResponse.prices); 
                    }
                                                            
	            } else {
                    if (dataResponse.errors.length) {
                        let errorList = dataResponse.errors.join('.');
                        alert(errorList);
                    }    
	            }
            },
            error: function (error) {
                $('#preloader').addClass('hidden');
                console.log('Error: ' + error);
            }
        });
                
    }
JS;
$this->registerJs($js);

?>

<?php
$this->registerCss('
    .nav-tabs li a.active {
        font-weight: 900;
    } 
');
?>

<?php $form = ActiveForm::begin([
    'action' => \yii\helpers\Url::to(['quote/save']),
    'errorCssClass' => '',
    'successCssClass' => '',
    'id' => $formID
]) ?>
<!------------- Add/Edit Alternative Quote Form ------------->
<div class="alternatives__item">
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
                            'class' => 'form-control alt-quote-price'
                        ]) ?>
                    </td>
                    <td class="td-input">
                        <?= $form->field($price, '[' . $index . ']net', [
                            'options' => [
                                'class' => 'input-group',
                            ],
                            'template' => '<span class="input-group-addon">$</span>{input}'
                        ])->textInput([
                            'class' => 'form-control alt-quote-price'
                        ]) ?>
                    </td>
                    <td class="td-input">
                        <?= $form->field($price, '[' . $index . ']fare', [
                            'options' => [
                                'class' => 'input-group',
                            ],
                            'template' => '<span class="input-group-addon">$</span>{input}'
                        ])->textInput([
                            'class' => 'form-control alt-quote-price'
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
                            'readonly' => true
                        ]) ?>
                    </td>
                    <td class="td-input">
                        <?= $form->field($price, '[' . $index . ']mark_up', [
                            'options' => [
                                'class' => 'input-group',
                            ],
                            'template' => '<span class="input-group-addon">$</span>{input}'
                        ])->textInput([
                            'class' => 'form-control alt-quote-price mark-up'
                        ]) ?>
                    </td>
                    <td class="td-input text-right">
                        <?php if (!in_array($price->passenger_type, $applyBtn) && $paxCntTypes[$price->passenger_type] > 1) {
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
                        } ?>
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
                            ])->dropDownList($quote::getGDSName(), [
                                'prompt' => 'Select',
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
                            'options' => ['placeholder' => 'Select'],
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
                            'class' => 'alt-quote-price',
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
                <?php if (!isset($project_id)) {
                    $project_id = $lead->project_id;
                } ?>
                <?php if (isset($project_id)): ?>
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
                <li id="reservation-dump-tab">
                    <?= Html::a('Reservation Dump', sprintf('#r-dump-%d', $quote->id), ['data-toggle' => 'tab', 'class' => 'active']) ?>
                </li>
                <li>
                	<?= Html::a('Pricing', sprintf('#r-dump-pane-%d', $quote->id), ['data-toggle' => 'tab']) ?>
                </li>
                <li id="box-gds-tab" style="display: none;">
                	<?= Html::a('GDS Dump', sprintf('#r-prepare_dump-%d', $quote->id), ['data-toggle' => 'tab']) ?>
                </li>
            </ul>
            <div class="tab-content">
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
                <div id="<?= sprintf('r-prepare_dump-%d', $quote->id) ?>" class="prepare_dump_box tab-pane fade in">
                    <?php echo Html::textarea('prepare_dump', null,
                        ['id' => 'prepare_dump', 'rows' => 13, 'class' => 'form-control'])
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="btn-wrapper">
        <?= Html::button('<i class="glyphicon glyphicon-remove-circle"></i> Cancel', [
            'id' => 'cancel-alt-quote',
            'class' => 'btn btn-danger'
        ]) ?>
        <?php
        $applied = Quote::findOne([
            'status' => Quote::STATUS_APPLIED,
            'lead_id' => $quote->lead_id
        ]);
        if (($quote->isNewRecord || $quote->status == $quote::STATUS_CREATED) && $applied === null) : ?>
            <?= Html::button('<i class="fa fa-save"></i> Save', [
                'id' => 'save-alt-quote',
                'class' => 'btn btn-primary'
            ]) ?>
        <?php endif; ?>
        <?php if ($quote->isNewRecord) :?>
            <?= Html::button('<i class="fa fa-recycle"></i> Import from GDS dump', [
                'id' => 'prepare_dump_btn',
                'class' => 'btn btn-warning',
                'style' => 'display: none',
            ]) ?>
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
