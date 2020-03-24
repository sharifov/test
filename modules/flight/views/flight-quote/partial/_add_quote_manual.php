<?php

use common\models\Airline;
use common\models\Employee;
use kartik\select2\Select2;
use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuoteCreateForm;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuotePaxPriceForm;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var $this View
 * @var $message null|string
 * @var $createQuoteForm FlightQuoteCreateForm
 * @var $flight Flight
 * @var $pjaxReloadId string
 */

$paxCntTypes = [
	FlightPax::PAX_ADULT => $flight->fl_adults,
	FlightPax::PAX_CHILD => $flight->fl_children,
	FlightPax::PAX_INFANT => $flight->fl_infants
];
$pjaxId = 'pjax-container-prices';
?>

<?php if($message): ?>
	<?= \yii\bootstrap4\Alert::widget([
	        'options' => [
                'class' => 'alert alert-danger'
            ],
            'body' => $message
    ]) ?>
<?php else: ?>
	<div class="row">
		<div class="col-md-12">
            <?php Pjax::begin(['id' => $pjaxId, 'enableReplaceState' => false, 'enablePushState' => false, 'timeout' => 2000]) ?>
            <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]) ?>

            <?= $form->errorSummary($createQuoteForm) ?>

            <?= Html::hiddenInput('flightId', $flight->fl_id) ?>
            <?= Html::hiddenInput('pjaxReloadId', $pjaxReloadId) ?>
            <table class="table table-striped table-neutral">
                <thead>
                <tr>
                    <th>Pax Type</th>
                    <th>X</th>
                    <th>Fare</th>
                    <th></th>
                    <th>Taxes</th>
                    <th></th>
                    <th>Net Price</th>
                    <th></th>
                    <th>Mark-up</th>
                    <th></th>
                    <th>Selling Price</th>
<!--                    <th></th>-->
                </tr>
                </thead>
                <tbody>
				<?php
				$applyBtn = [];
				/** @var $price FlightQuotePaxPriceForm */
				foreach ($createQuoteForm->prices as $index => $price) : ?>
                    <?= $form->field($price, '[' . $index . ']paxCode')->hiddenInput()->label(false) ?>
                    <?= $form->field($price, '[' . $index . ']paxCodeId')->hiddenInput()->label(false) ?>
                    <tr class="pax-type-<?= $price->paxCode ?>" id="price-index-<?= $index ?>">
                        <td class="td-input">
							<?= $price->paxCode ?>
                        </td>
                        <td><?= $price->cnt ?></td>
                        <td class="td-input">
							<?= $form->field($price, '[' . $index . ']fare')->input('number', [
								'class' => 'form-control alt-quote-price',
                                'min' => FlightQuotePaxPriceForm::getMinDecimalVal(),
                                'max' => FlightQuotePaxPriceForm::getMaxDecimalVal(),
                                'step' => 0.01
							])->label(false) ?>
                        </td>
                        <td>+</td>
                        <td class="td-input">
							<?= $form->field($price, '[' . $index . ']taxes')->input('number', [
								'class' => 'form-control alt-quote-price',
								'readonly' => true,
								'min' => FlightQuotePaxPriceForm::getMinDecimalVal(),
								'max' => FlightQuotePaxPriceForm::getMaxDecimalVal(),
								'step' => 0.01
							])->label(false) ?>
                        </td>
                        <td>=</td>
                        <td class="td-input">
							<?= $form->field($price, '[' . $index . ']net')->input('number', [
								'class' => 'form-control alt-quote-price',
								'min' => FlightQuotePaxPriceForm::getMinDecimalVal(),
								'max' => FlightQuotePaxPriceForm::getMaxDecimalVal(),
								'step' => 0.01
							])->label(false) ?>
                        </td>
                        <th>+</th>
                        <td class="td-input">
							<?= $form->field($price, '[' . $index . ']markup')->input('number', [
								'class' => 'form-control alt-quote-price mark-up',
								'min' => FlightQuotePaxPriceForm::getMinDecimalVal(),
								'max' => FlightQuotePaxPriceForm::getMaxDecimalVal(),
								'step' => 0.01
							])->label(false) ?>
                        </td>
                        <th>=</th>
                        <td class="td-input">
							<?= $form->field($price, '[' . $index . ']selling')->input('number', [
								'class' => 'form-control alt-quote-price',
								'min' => FlightQuotePaxPriceForm::getMinDecimalVal(),
								'max' => FlightQuotePaxPriceForm::getMaxDecimalVal(),
								'step' => 0.01
							])->label(false) ?>
                        </td>
                        <td class="td-input text-right">
							<?php /* if ($paxCntTypes[$price->paxCode] > 1 && !in_array($price->paxCodeId, $applyBtn, false)) {
								$applyBtn[] = $price->paxCodeId;
								echo Html::button('<i class="fa fa-copy"></i>', [
									'title' => '',
									'data-toggle' => 'tooltip',
									'data-placement' => 'top',
									'data-original-title' => 'Clone Price for Pax Type ' . $price->paxCode,
									'class' => 'btn btn-primary btn-sm clone-alt-price-by-type',
									'data-price-index' => $index,
									'data-type' => $price->paxCodeId
								]);
							} */ ?>
                        </td>
                    </tr>
				<?php endforeach; ?>
                </tbody>
            </table>

            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
							<?= $form->field($createQuoteForm, 'recordLocator', ['labelOptions' => ['class' => 'control-label']])->textInput() ?>

							<?= $form->field($createQuoteForm, 'pcc', ['labelOptions' => ['class' => 'control-label']])->textInput() ?>

							<?= $form->field($createQuoteForm, 'cabin', ['labelOptions' => ['class' => 'control-label']])->dropDownList(Flight::getCabinClassList(), ['prompt' => '---']) ?>

							<?= $form->field($createQuoteForm, 'quoteCreator', ['labelOptions' => ['class' => 'control-label']])->dropDownList(Employee::getListByProject($flight->flProduct->prLead->project_id, false), ['prompt' => '---']) ?>
                        </div>
                        <div class="col-md-6">
							<?= $form->field($createQuoteForm, 'gds', ['labelOptions' => ['class' => 'control-label']])->dropDownList(FlightQuote::getGdsList(), ['prompt' => '---']) ?>

							<?= $form->field($createQuoteForm, 'tripType', ['labelOptions' => ['class' => 'control-label']])->dropDownList(Flight::getTripTypeList(), ['prompt' => '---']) ?>

                            <label for="" class="control-label"><?= $createQuoteForm->getAttributeLabel('validatingCarrier') ?></label>
							<?= $form->field($createQuoteForm, 'validatingCarrier', [
								'options' => [
									'tag' => false,
								],
								'template' => '{input}',
                                'labelOptions' => ['class' => 'control-label']
							])->widget(Select2::class, [
								'data' => Airline::getAirlinesMapping(true),
								'options' => ['placeholder' => 'Select'],
								'pluginOptions' => [
									'allowClear' => false
								],
                                'size' => Select2::SIZE_SMALL
							])->label(true) ?>

							<?= $form->field($createQuoteForm, 'fareType', ['labelOptions' => ['class' => 'control-label'], 'options' => ['style' => 'margin-top: 9px;']])->dropDownList(FlightQuote::getFareTypeList(), ['prompt' => '---']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
							<?= $form->field($createQuoteForm, 'reservationDump', ['labelOptions' => ['class' => 'control-label']])->textarea() ?>
                        </div>
                        <div class="col-md-6">
							<?= $form->field($createQuoteForm, 'pricingInfo', ['labelOptions' => ['class' => 'control-label']])->textarea() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <?= Html::submitButton('<i class="fa fa-save"></i> Create', ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
            <?php Pjax::end() ?>
		</div>
	</div>
    <?php $js = <<<JS
    var timeout;
    $('.alt-quote-price').on('keyup', function (event) {
        let form = $(this).closest('form');
        
        clearTimeout(timeout);
        
        timeout = setTimeout(function () {
        $.pjax.submit()
        }, 2000);
    })
JS;
    $this->registerJs($js);
?>
<?php endif; ?>
