<?php
/**
 * @var $lead Lead
 * @var $quote Quote
 * @var $prices QuotePrice[]
 */

use common\models\Lead;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\models\Quote;
use common\models\QuotePrice;
use kartik\select2\Select2;
use common\models\Airline;

$altQuotePriceUrl = \yii\helpers\Url::to(['alt-quote/alt-price', 'altQuoteId' => $quote->id]);
$formID = sprintf('alt-quote-info-form-%d', $quote->id);

$paxCntTypes = [
    QuotePrice::PASSENGER_ADULT => $lead->adults,
    QuotePrice::PASSENGER_CHILD => $lead->children,
    QuotePrice::PASSENGER_INFANT => $lead->infants
];

?>

<?php $form = ActiveForm::begin([
    'action' => \yii\helpers\Url::to(['alt-quote/save']),
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
        <table class="table table-striped table-neutral">
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
                                'prompt' => 'Select'
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
                            ])->dropDownList(Lead::getFlightType()) ?>
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
                            ])->dropDownList(Lead::getCabin()) ?>
                        </div>
                    </td>
                    <th><label for="v-carrier">Validating Carrier</label></th>
                    <td class="td-input">
                        <?= $form->field($quote, 'main_airline_code', [
                            'options' => [
                                'tag' => false,
                            ],
                            'template' => '{input}'
                        ])->widget(Select2::classname(), [
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
                            'template' => '{input}'
                        ])->label(false); ?>
                        <label for="<?= Html::getInputId($quote, 'check_payment') ?>"></label>
                    </td>
                    <th colspan="2" class="td-input"></th>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-sm-6">
            <ul class="nav nav-tabs">
                <li class="active">
                    <?= Html::a('Reservation Dump', sprintf('#r-dump-%d', $quote->id), ['data-toggle' => 'tab']) ?>
                </li>
            </ul>
            <div class="tab-content">
                <div id="<?= sprintf('r-dump-%d', $quote->id) ?>" class="tab-pane fade in active">
                    <?= $form->field($quote, 'reservation_dump', [
                        'options' => [
                            'tag' => false,
                        ],
                        'template' => '{input}'
                    ])->textarea([
                        'rows' => 5
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="btn-wrapper">
        <?= Html::button('<span class="btn-icon"><i class="glyphicon glyphicon-remove-circle"></i></span><span>Cancel</span>', [
            'id' => 'cancel-alt-quote',
            'class' => 'btn btn-danger btn-with-icon'
        ]) ?>
        <?php
        $applied = Quote::findOne([
            'status' => Quote::STATUS_APPLIED,
            'lead_id' => $quote->lead_id
        ]);
        if (($quote->isNewRecord || $quote->status == $quote::STATUS_CREATED) && $applied === null) : ?>
            <?= Html::button('<span class="btn-icon"><i class="fa fa-save"></i></span><span>Save</span>', [
                'id' => 'save-alt-quote',
                'class' => 'btn btn-primary btn-with-icon'
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
                    <?= Html::button('<span class="btn-icon"><i class="glyphicon glyphicon-remove-circle"></i></span><span>Cancel</span>', [
                        'id' => 'cancel-confirm-quote',
                        'class' => 'btn btn-danger btn-with-icon'
                    ]) ?>
                    <?= Html::button('<span class="btn-icon"><i class="fa fa-save"></i></span><span>Save</span>', [
                        'id' => 'confirm-alt-quote',
                        'class' => 'btn btn-primary btn-with-icon'
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
