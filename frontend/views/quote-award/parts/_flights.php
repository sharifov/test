<?php

use common\models\Employee;
use common\models\Lead;
use common\models\Quote;
use src\model\flightQuoteLabelList\service\FlightQuoteLabelListDictionary;
use src\model\flightQuoteLabelList\service\FlightQuoteLabelListService;
use yii\bootstrap\ActiveForm;
use modules\quoteAward\src\dictionary\AwardProgramDictionary;
use src\helpers\lead\LeadHelper;
use src\services\parsingDump\lib\ParsingDump;
use kartik\select2\Select2;
use common\models\Airline;
use yii\helpers\Html;

/**
 * @var $model \modules\quoteAward\src\forms\AwardQuoteForm
 */

$form = ActiveForm::begin([
    'action' => \yii\helpers\Url::to(['quote-award/save']),
    'id' => 'alt-award-quote-info-form'
]) ?>
<div style="margin-top: 15px">

    <div style="margin-top: 15px">
        <?php if (count($model->flights)) : ?>
            <h5 style="font-weight:bold">Flight List</h5>
            <table class="table table-neutral table-award-flight" id="price-table">
                <thead>
                <tr>
                    <th></th>
                    <th>Nr</th>
                    <th>Name</th>
                    <th>Cabin</th>
                    <th>ADT</th>
                    <th>CHD</th>
                    <th>INF</th>
                    <th>GDS</th>
                    <th>PPC</th>
                    <th>Validating Carrier</th>
                    <th>Record Locator</th>
                    <th>Fare Type</th>
                    <th>Booking Type</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 0;
                foreach ($model->flights as $index => $flight) :
                    $i++;
                    ?>
                    <tr id="flight-index-<?= $flight->id ?>">
                        <td style="width:30px" class="text-center">
                            <?php if ($flight->id != 0) : ?>
                                <a class="btn btn-default js-remove-flight-award"
                                   data-inner='<i class="fa fa-trash" aria-hidden="true"></i>'
                                   data-id="<?= $flight->id ?>"
                                   data-class='btn btn-default js-remove-flight-award'
                                   href="javascript:void(0)">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td style="width:35px"><?= $i ?></td>
                        <td style="width: 60px"><?= 'Flight ' . ($index + 1) ?></td>
                        <td style="width: 120px">
                            <?= $form->field($flight, '[' . $index . ']id', ['template' => '{input}', 'options' => ['tag' => false]])->hiddenInput()->label(false) ?>
                            <?= $form->field($flight, '[' . $index . ']cabin', [
                            ])->dropDownList(LeadHelper::cabinList(), [
                                'prompt' => '---'])->label(false) ?></td>
                        <td style="width:55px"><?= $form->field($flight, '[' . $index . ']adults')->dropDownList(LeadHelper::adultsChildrenInfantsList(), ['prompt' => '-', 'class' => 'form-control js-pax-award'])->label(false) ?></td>
                        <td style="width:55px"><?= $form->field($flight, '[' . $index . ']children')->dropDownList(LeadHelper::adultsChildrenInfantsList(), ['prompt' => '-', 'class' => 'form-control js-pax-award'])->label(false) ?></td>
                        <td style="width:55px"><?= $form->field($flight, '[' . $index . ']infants')->dropDownList(LeadHelper::adultsChildrenInfantsList(), ['prompt' => '-', 'class' => 'form-control js-pax-award'])->label(false) ?></td>
                        <td style="width: 120px"><?= $form->field($flight, '[' . $index . ']gds')->dropDownList(ParsingDump::QUOTE_GDS_TYPE_MAP, ['prompt' => '---'])->label(false) ?></td>
                        <td style="width: 120px"><?= $form->field($flight, '[' . $index . ']ppc')->textInput()->label(false) ?></td>
                        <td style="width: 120px"><?= $form->field($flight, '[' . $index . ']validationCarrier')
                                ->widget(Select2::class, [
                                    'data' => Airline::getAirlinesMapping(true),
                                    'options' => ['placeholder' => '---'],
                                    'pluginOptions' => [
                                        'allowClear' => false
                                    ],
                                ])->label(false) ?></td>

                        <td style="width: 105px"><?= $form->field($flight, '[' . $index . ']recordLocator')->textInput()->label(false) ?></td>
                        <td style="width: 120px"><?= $form->field($flight, '[' . $index . ']fareType')->dropDownList(Quote::getFareType(), ['prompt' => '---',])->label(false) ?></td>
                        <td style="width: 120px"><?= $form->field($flight, '[' . $index . ']quoteProgram')->dropDownList(AwardProgramDictionary::geList(), ['data-id' => $flight->id, 'class' => 'form-control js-flight-quote-program'])->label(false) ?></td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
        <?php endif; ?>

    </div>
    <div class="x_panel">
        <div class="row x_content">
            <div class="col-md-3">
                <?= $form->field($model, 'trip_type', [
                    'options' => [
                        'tag' => false,
                    ],
                ])->dropDownList(Lead::getFlightTypeList()) ?>
            </div>


            <div class="col-md-3">
                <?= $form->field($model, 'labels')->widget(Select2::class, [
                    'data' => FlightQuoteLabelListService::getListKeyDescription(FlightQuoteLabelListDictionary::MANUAL_CREATE_LABELS),
                    'size' => Select2::SIZE_SMALL,
                    'pluginOptions' => [
                        'width' => '100%',
                    ],
                    'options' => [
                        'placeholder' => '',
                        'multiple' => true,
                    ],]) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, 'employee_id')->dropDownList(Employee::getListByProject($lead->project_id, false)) ?>
            </div>

            <div class="col-md-3">
                <label class="control-label" for="<?= Html::getInputId($model, 'checkPayment') ?>">Check payment</label>
                <div class="custom-checkbox">
                    <?= $form->field($model, 'checkPayment', [
                        'options' => [
                            'tag' => false,
                        ],
                    ])->checkbox(['class' => 'alt-quote-price alt-award-quote-price js-check-payment', 'template' => '{input}']) ?>
                    <label for="<?= Html::getInputId($model, 'checkPayment') ?>"></label>
                </div>

            </div>

        </div>

    </div>
    <?= $this->render('_segment', ['model' => $model, 'form' => $form]) ?>

    <?= $this->render('_price_list', ['model' => $model, 'form' => $form]) ?>

</div>
<div class="form-group">
    <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Save Quote', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end() ?>
<style>
    .table-award-flight td, .table-award-flight th {
        padding: 5px !important;
    }

</style>
