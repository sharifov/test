<?php

use common\models\Airline;
use common\models\Employee;
use common\models\Lead;
use common\models\Quote;
use kartik\select2\Select2;
use modules\quoteAward\src\dictionary\AwardProgramDictionary;
use modules\quoteAward\src\dictionary\ProductTypeDictionary;
use modules\quoteAward\src\entities\QuoteFlightProgram;
use modules\quoteAward\src\entities\QuoteFlightProgramQuery;
use src\helpers\lead\LeadHelper;
use src\model\flightQuoteLabelList\service\FlightQuoteLabelListDictionary;
use src\model\flightQuoteLabelList\service\FlightQuoteLabelListService;
use src\services\parsingDump\lib\ParsingDump;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var $model \modules\quoteAward\src\forms\AwardQuoteForm
 */

$form = ActiveForm::begin([
    'action' => \yii\helpers\Url::to(['quote-award/save']),
    'id' => 'alt-award-quote-info-form'
]) ?>
<div style="margin-top: 15px">
    <div>
        <div class="row">
            <div class="col-md-2 d-flex flex-row pr-0">
                <div class="text-nowrap pt-1 pr-2">Trip Type</div>
                <?= $form->field($model, 'trip_type', ['options' => ['tag' => false]])
                    ->dropDownList(Lead::getFlightTypeList())
                    ->label(false) ?>
            </div>

            <div class="col-md-2 d-flex flex-row pr-0">
                <div class="text-nowrap pt-1 pr-2">Cabine</div>
                <?= $form->field($model, 'cabin', [])
                    ->dropDownList(LeadHelper::cabinList(), ['prompt' => 'Select Cabin'])
                    ->label(false) ?>
            </div>
        </div>
    </div>

    <?= $this->render('_segment', ['model' => $model, 'form' => $form]) ?>

    <div style="margin-top: 15px">
        <?php if (count($model->flights) > 0) : ?>
            <ul class="nav nav-tabs">
                <?php foreach ($model->flights as $key => $flight) : ?>
                    <li class="<?= ($tab === $key ? 'active' : '') ?> nav-item">
                        <a data-toggle="tab" href="#tab-<?= $key ?>"
                           class="nav-link <?= ($tab === $key ? 'active' : '') ?> js-flight-tab" data-id="<?= $key ?>">
                            <?= 'Flight ' . ($key + 1) ?>
                            <?php if ($flight->id != 0) : ?>
                                <span class="js-remove-flight-award"
                                      data-inner='<i class="fa fa-times" aria-hidden="true"></i>'
                                      data-id="<?= $flight->id ?>"
                                      data-class='js-remove-flight-award'>
                                <i class="fa fa-times text-danger" aria-hidden="true"></i>
                            </span>
                            <?php endif; ?>
                        </a>


                    </li>
                <?php endforeach; ?>
                <?php if (count($model->flights) < 5) : ?>
                    <div class="d-flex align-items-center justify-content-center"
                         style="margin-left: 15px;">
                        <a class="btn btn-add-flight"
                           id="js-add-flight-award"
                           data-inner='<i class="fa fa-plus" aria-hidden="true"></i> Add Flight'
                           data-class='btn btn-add-flight'
                           href="javascript:void(0)">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Flight
                        </a>
                    </div>
                <?php endif; ?>

            </ul>


            <div class="tab-content">
                <?php foreach ($model->flights as $index => $flight) : ?>
                    <div id="tab-<?= $index ?>"
                         class="tab-pane fade in <?= ($tab === $index ? 'active show' : '') ?>">

                        <div style="border: 1px solid #C7CED5;">
                            <div style="padding: 10px">
                                <div class="row">
                                    <div class="col-md-3 d-flex">
                                        <div style="margin-right: 10px; margin-top: 5px; width: 210px">
                                            <label class="control-label"
                                                   for="<?= Html::getInputId($flight, '[' . $index . ']quoteProgram') ?>">Type
                                                of booking</label>
                                        </div>
                                        <div style="width: 100%">
                                            <?= $form->field($flight, '[' . $index . ']quoteProgram', ['template' => '{input}', 'options' => ['tag' => false]])->dropDownList(AwardProgramDictionary::geList(), ['data-id' => $flight->id, 'class' => 'form-control js-flight-quote-program'])->label(false) ?>
                                        </div>
                                    </div>

                                    <div class="col-md-2 d-flex">
                                        <div style="margin-top: 5px; margin-right: 5px; width: 110px">
                                            <label class="control-label"
                                                   for="<?= Html::getInputId($flight, '[' . $index . ']cabin') ?>">Cabin</label>
                                        </div>
                                        <div style="width: 100%">
                                            <?= $form->field($flight, '[' . $index . ']id', ['template' => '{input}', 'options' => ['tag' => false]])->hiddenInput()->label(false) ?>
                                            <?= $form->field($flight, '[' . $index . ']cabin', [
                                            ])->dropDownList(LeadHelper::cabinList(), [
                                                'prompt' => '---'])->label(false) ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6 d-flex">
                                        <div style="margin-top: 5px; margin-right: 5px">
                                            <label class="control-label"
                                                   for="<?= Html::getInputId($flight, '[' . $index . ']adults') ?>">ADT</label>
                                        </div>
                                        <div style="margin-right: 10px">
                                            <?= $form->field($flight, '[' . $index . ']adults')->dropDownList(LeadHelper::adultsChildrenInfantsList(), ['prompt' => '-', 'class' => 'form-control js-pax-award'])->label(false) ?>
                                        </div>

                                        <div style="margin-top: 5px; margin-right: 5px">
                                            <label class="control-label"
                                                   for="<?= Html::getInputId($flight, '[' . $index . ']children') ?>">CHD</label>
                                        </div>
                                        <div style="margin-right: 10px">
                                            <?= $form->field($flight, '[' . $index . ']children')->dropDownList(LeadHelper::adultsChildrenInfantsList(), ['prompt' => '-', 'class' => 'form-control js-pax-award'])->label(false) ?>
                                        </div>

                                        <div style="margin-top: 5px; margin-right: 5px">
                                            <label class="control-label"
                                                   for="<?= Html::getInputId($flight, '[' . $index . ']infants') ?>">INF</label>
                                        </div>
                                        <div style="margin-right: 10px">
                                            <?= $form->field($flight, '[' . $index . ']infants')->dropDownList(LeadHelper::adultsChildrenInfantsList(), ['prompt' => '-', 'class' => 'form-control js-pax-award'])->label(false) ?>
                                        </div>

                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-3 d-flex">
                                        <div style="margin-right: 10px; margin-top: 5px; width: 210px">
                                            <label class="control-label"
                                                   for="<?= Html::getInputId($flight, '[' . $index . ']validationCarrier') ?>">Validating
                                                Carrier</label>
                                        </div>
                                        <div style="width: 100%">
                                            <?= $form->field($flight, '[' . $index . ']validationCarrier')
                                                ->widget(Select2::class, [
                                                    'data' => Airline::getAirlinesMapping(true),
                                                    'options' => ['placeholder' => '---'],
                                                    'pluginOptions' => [
                                                        'allowClear' => false
                                                    ],
                                                ])->label(false) ?>
                                        </div>
                                    </div>

                                    <div class="col-md-2 d-flex">
                                        <div style="margin-right: 10px; margin-top: 5px; width: 110px">
                                            <label class="control-label"
                                                   for="<?= Html::getInputId($flight, '[' . $index . ']gds') ?>">GDS</label>
                                        </div>
                                        <div style="width: 100%">
                                            <?= $form->field($flight, '[' . $index . ']gds')->dropDownList(ParsingDump::QUOTE_GDS_TYPE_MAP, ['prompt' => '---'])->label(false) ?>
                                        </div>
                                    </div>

                                    <div class="col-md-2 d-flex">
                                        <div style="margin-right: 10px; margin-top: 5px;">
                                            <label class="control-label"
                                                   for="<?= Html::getInputId($flight, '[' . $index . ']pcc') ?>">PCC</label>
                                        </div>
                                        <div style="width: 100%">
                                            <?= $form->field($flight, '[' . $index . ']pcc')->textInput()->label(false) ?>
                                        </div>
                                    </div>

                                    <div class="col-md-3 d-flex">
                                        <div style="margin-right: 10px; margin-top: 5px; width: 150px">
                                            <label class="control-label"
                                                   for="<?= Html::getInputId($flight, '[' . $index . ']recordLocator') ?>">Record
                                                Locator</label>
                                        </div>
                                        <div style="width: 100%">
                                            <?= $form->field($flight, '[' . $index . ']recordLocator')->textInput()->label(false) ?>
                                        </div>
                                    </div>

                                    <div class="col-md-2 d-flex">
                                        <div style="margin-right: 10px; margin-top: 5px; width: 110px">
                                            <label class="control-label"
                                                   for="<?= Html::getInputId($flight, '[' . $index . ']fareType') ?>">Fare
                                                Type</label>
                                        </div>
                                        <div style="width: 100%">
                                            <?= $form->field($flight, '[' . $index . ']fareType')->dropDownList(Quote::getFareType(), ['prompt' => '---',])->label(false) ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-md-3 d-flex">
                                        <div style="margin-right: 10px; margin-top: 5px; width: 210px">
                                            <label class="control-label"
                                                   for="<?= Html::getInputId($flight, '[' . $index . ']productType') ?>">Product</label>
                                        </div>
                                        <div style="width: 100%">
                                            <?= $form->field($flight, '[' . $index . ']productType', [
                                            ])->dropDownList(ProductTypeDictionary::getList(), [
                                                'prompt' => '---'])->label(false) ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="js-flight-wrap">
                                    <div class="js-display-quote-program <?= $flight->isRequiredAwardProgram() ? '' : 'd-none' ?>"
                                         data-id="<?= $flight->id ?>">
                                        <div class="row">
                                            <div class="col-lg-3 d-flex">
                                                <div style="margin-right: 10px; margin-top: 5px; width: 210px">
                                                    <label class="control-label"
                                                           for="<?= Html::getInputId($flight, '[' . $index . ']awardProgram') ?>">Flight
                                                        Program</label>
                                                </div>
                                                <div style="width: 100%">
                                                    <?= $form->field($flight, '[' . $index . ']awardProgram')
                                                        ->dropDownList(
                                                            QuoteFlightProgram::getList(),
                                                            ['required' => 'required', 'class' => 'form-control js-award-program', 'options' => QuoteFlightProgramQuery::getListWithPpm()]
                                                        )->label(false) ?>
                                                </div>
                                            </div>
                                            <div class="col-md-2 d-flex">
                                                <div style="margin-right: 10px; margin-top: 5px; width: 110px">
                                                    <label class="control-label"
                                                           for="<?= Html::getInputId($flight, '[' . $index . ']ppm') ?>">PPM</label>
                                                </div>
                                                <div style="width: 100%">
                                                    <?= $form->field($flight, '[' . $flight->id . ']ppm')->textInput([
                                                        'class' => 'form-control alt-award-quote-price js-award-ppm',
                                                    ])->label(false) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>

            </div>

            <?= $this->render('_price_list', ['model' => $model, 'form' => $form]) ?>

            <div class="x_panel">
                <div class="row x_content">
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
                        <label class="control-label" for="<?= Html::getInputId($model, 'checkPayment') ?>">Check
                            payment</label>
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
        <?php endif; ?>
    </div>

    <div class="d-flex justify-content-center">
        <?php
        $applied = Quote::find()->andWhere([
            'status' => Quote::STATUS_APPLIED,
            'lead_id' => $lead->id
        ])->limit(1)->one();
        if ($applied === null) : ?>
            <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Save Quote', [
                'id' => 'save-alt-award-quote',
                'class' => 'btn btn-success'
            ]) ?>
        <?php endif; ?>
    </div>

    <?php ActiveForm::end() ?>
    <style>
        .table-award-flight td, .table-award-flight th {
            padding: 5px !important;
        }

        .btn-add-flight {
            border-radius: 19px;
            padding: 4px 16px;
            background: rgba(83, 162, 101, 0.2);
            color: #53A265;
        }

        .quote-award_wrap .table p, .quote-award_wrap .table .form-group {
            margin-bottom: 0;
        }

        .js-flight-tab {
            color: #495057;
        }

        .js-flight-tab.active {
            border-bottom: none;
            border-color: #53a265!important;
            color: #53a265!important;
        }
    </style>
