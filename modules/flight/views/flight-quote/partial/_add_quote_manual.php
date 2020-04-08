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
            <?php Pjax::begin([
                    'id' => $pjaxId,
                    'enableReplaceState' => false,
                    'enablePushState' => false,
                    'timeout' => 2000,
            ]) ?>
            <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1], 'id' => 'add-quote-form', 'enableClientValidation' => false]) ?>

            <?= $form->errorSummary($createQuoteForm) ?>

            <?= Html::hiddenInput('flightId', $flight->fl_id) ?>
            <?= Html::hiddenInput('pjaxReloadId', $pjaxReloadId) ?>
            <?= Html::hiddenInput('action', '', ['id' => 'add-quote-action']) ?>
            <?= $form->field($createQuoteForm, 'oldPrices')->hiddenInput()->label(false) ?>
            <table class="table table-striped table-neutral">
                <thead>
                <tr class="text-center">
                    <th>Pax Type</th>
                    <th>X</th>
                    <th>Fare</th>
                    <th>Taxes</th>
                    <th>Mark-up</th>
                    <th>SFP, %</th>
                    <th>Selling Price, $</th>
                    <th>Client Price, <?= $createQuoteForm->currencyCode ?></th>
<!--                    <th></th>-->
                </tr>
                </thead>
                <tbody>
				<?php
				$applyBtn = [];
				/** @var $price FlightQuotePaxPriceForm */
				foreach ($createQuoteForm->prices as $index => $price) : ?>
                    <?= $form->field($createQuoteForm, 'prices[' . $index . '][paxCode]')->hiddenInput()->label(false) ?>
                    <?= $form->field($createQuoteForm, 'prices[' . $index . '][paxCodeId]')->hiddenInput()->label(false) ?>
                    <?= $form->field($createQuoteForm, 'prices[' . $index . '][cnt]')->hiddenInput()->label(false) ?>
                    <tr class="pax-type-<?= $price->paxCode ?>" id="price-index-<?= $index ?>">
                        <td class="td-input">
							<?= $price->paxCode ?>
                        </td>
                        <td><?= $price->cnt ?></td>
                        <td class="td-input">
							<?= $form->field($createQuoteForm, 'prices[' . $index . '][fare]')->input('number', [
								'class' => 'form-control alt-quote-price',
                                'min' => FlightQuotePaxPriceForm::getMinDecimalVal(),
                                'max' => FlightQuotePaxPriceForm::getMaxDecimalVal(),
                                'step' => 0.01
							])->label(false) ?>
                        </td>
                        <td class="td-input">
							<?= $form->field($createQuoteForm, 'prices[' . $index . '][taxes]')->input('number', [
								'class' => 'form-control alt-quote-price',
//								'readonly' => true,
								'min' => FlightQuotePaxPriceForm::getMinDecimalVal(),
								'max' => FlightQuotePaxPriceForm::getMaxDecimalVal(),
								'step' => 0.01
							])->label(false) ?>
                        </td>
                        <td class="td-input">
							<?= $form->field($createQuoteForm, 'prices[' . $index . '][markup]')->input('number', [
								'class' => 'form-control alt-quote-price mark-up',
								'min' => FlightQuotePaxPriceForm::getMinDecimalVal(),
								'max' => FlightQuotePaxPriceForm::getMaxDecimalVal(),
								'step' => 0.01
							])->label(false) ?>
                        </td>
                        <td><?= $createQuoteForm->serviceFee ?></td>
                        <td class="text-right">
							<?= $form->field($createQuoteForm, 'prices[' . $index . '][selling]')->input('number', [
								'class' => 'form-control alt-quote-price',
								'min' => FlightQuotePaxPriceForm::getMinDecimalVal(),
								'max' => FlightQuotePaxPriceForm::getMaxDecimalVal(),
								'step' => 0.01
							])->label(false) ?>
                        </td>
                        <td class="td-input text-right">
							<?= $form->field($createQuoteForm, 'prices[' . $index . '][clientSelling]')->input('number', [
								'class' => 'form-control alt-quote-price',
								'readonly' => true,
							])->label(false) ?>
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
							<?= $form->field($createQuoteForm, 'pcc', ['labelOptions' => ['class' => 'control-label', 'max' => 10]])->textInput() ?>

							<?= $form->field($createQuoteForm, 'gds', ['labelOptions' => ['class' => 'control-label']])->dropDownList(FlightQuote::getGdsList(), ['prompt' => '---']) ?>

							<?= $form->field($createQuoteForm, 'cabin', ['labelOptions' => ['class' => 'control-label']])->dropDownList(Flight::getCabinClassList(), ['prompt' => '---']) ?>

							<?= $form->field($createQuoteForm, 'quoteCreator', ['labelOptions' => ['class' => 'control-label']])->widget(sales\widgets\UserSelect2Widget::class, [
							        'data' => [$createQuoteForm->quoteCreator => Employee::findOne($createQuoteForm->quoteCreator)->username ]
                            ]) ?>
                        </div>
                        <div class="col-md-6">
							<?= $form->field($createQuoteForm, 'recordLocator', ['labelOptions' => ['class' => 'control-label', 'max' => 8]])->textInput() ?>

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
                        <div class="col-md-12">
							<?= $form->field($createQuoteForm, 'reservationDump', ['labelOptions' => ['class' => 'control-label']])->textarea(['rows' => 7]) ?>
                        </div>
                        <div class="col-md-12">
							<?= $form->field($createQuoteForm, 'pricingInfo', ['labelOptions' => ['class' => 'control-label']])->textarea(['class' => 'apply-pricing-field form-control', 'rows' => 7]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <?= Html::submitButton('<i class="fa fa-save"></i> Create', ['class' => 'btn btn-success', 'id' => 'add-quote-submit-btn']) ?>
							<?= Html::submitButton('<i class="fas fa-file-code"></i> Apply Pricing Info', ['class' => 'btn btn-info ' . (empty($createQuoteForm->pricingInfo) ? ' d-none' : ''), 'id' => 'btn-apply-pricing']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

			<?php
			$actionApply = FlightQuoteCreateForm::ACTION_APPLY_PRICING_INFO;
			$actionCalculate = FlightQuoteCreateForm::ACTION_CALCULATE_PRICES;
			$js = <<<JS
            var form = $('#add-quote-form');
            var pricingDumpField = $('.apply-pricing-field', form);
            var timeout;
            form.on('keyup', '.alt-quote-price', function (event) {
                clearTimeout(timeout);
                
                timeout = setTimeout(function () {
                    $('#add-quote-action').val('$actionCalculate');
                    $('#add-quote-form').submit();
                }, 1500);
            });
            $('body').off('click', '#btn-apply-pricing').on('click', '#btn-apply-pricing', function (e) {
                $('#add-quote-action').val('$actionApply');
                $('#add-quote-form').submit();
            });
            pricingDumpField.on('change', function () {
                if ($(this).val() == '') {
                    $('#btn-apply-pricing').addClass('d-none');
                } else {
                    $('#btn-apply-pricing').removeClass('d-none');
                }
            });
            form.on('click', '#add-quote-submit-btn', function () {
                $('#add-quote-action').val('');
            });
JS;
			$this->registerJs($js);
			?>

            <?php Pjax::end() ?>
		</div>
	</div>
<?php endif; ?>
