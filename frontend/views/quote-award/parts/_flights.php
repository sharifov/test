<?php

use common\models\Quote;
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
        <h5>Flight List</h5>
        <div style="margin-top: 15px">
            <?php if (count($model->flights)) : ?>
                <table class="table table-neutral" id="price-table">
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
                        <th>Validating Carrier</th>
                        <th>Record Locator</th>
                        <th>Fare Type</th>
                        <th>Program</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 0;
                    foreach ($model->flights as $index => $flight) :
                        $i++;
                        ?>
                        <tr id="flight-index-<?= $flight->id ?>">
                            <td style="width:35px">
                                <?php if ($flight->id != 1) : ?>
                                    <a class="btn btn-danger js-remove-flight-award"
                                       data-inner='<i class="glyphicon glyphicon-remove" aria-hidden="true"></i>'
                                       data-id="<?= $flight->id ?>"
                                       data-class='btn btn-danger js-remove-flight-award'
                                       href="javascript:void(0)">
                                        <i class="glyphicon glyphicon-remove" aria-hidden="true"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td style="width:35px"><?= $i ?></td>
                            <td style="width: 100px"><?= 'Flight ' . $flight->id ?></td>
                            <td>
                                <?= $form->field($flight, '[' . $index . ']id', ['template' => '{input}', 'options' => ['tag' => false]])->hiddenInput()->label(false) ?>
                                <?= $form->field($flight, '[' . $index . ']cabin', [
                                ])->dropDownList(LeadHelper::cabinList(), [
                                    'prompt' => '---'])->label(false) ?></td>
                            <td style="width:85px"><?= $form->field($flight, '[' . $index . ']adults')->textInput(['type' => 'number', 'class' => 'form-control js-pax-award'])->label(false) ?></td>
                            <td style="width:85px"><?= $form->field($flight, '[' . $index . ']children')->textInput(['type' => 'number', 'class' => 'form-control js-pax-award'])->label(false) ?></td>
                            <td style="width:85px"><?= $form->field($flight, '[' . $index . ']infants')->textInput(['type' => 'number', 'class' => 'form-control js-pax-award'])->label(false) ?></td>
                            <td style="width: 120px"><?= $form->field($flight, '[' . $index . ']gds')->dropDownList(ParsingDump::QUOTE_GDS_TYPE_MAP, ['prompt' => '---'])->label(false) ?></td>
                            <td><?= $form->field($flight, '[' . $index . ']validationCarrier')
                                    ->widget(Select2::class, [
                                        'data' => Airline::getAirlinesMapping(true),
                                        'options' => ['placeholder' => '---'],
                                        'pluginOptions' => [
                                            'allowClear' => false
                                        ],
                                    ])->label(false) ?></td>

                            <td style="width: 105px"><?= $form->field($flight, '[' . $index . ']recordLocator')->textInput()->label(false) ?></td>
                            <td><?= $form->field($flight, '[' . $index . ']fareType')->dropDownList(Quote::getFareType(), ['prompt' => '---',])->label(false) ?></td>
                            <td><?= $form->field($flight, '[' . $index . ']quoteProgram')->dropDownList(AwardProgramDictionary::geList(), ['data-id' => $flight->id, 'class' => 'form-control js-flight-quote-program'])->label(false) ?></td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?= $this->render('_segment', ['model' => $model, 'form' => $form]) ?>

        <?= $this->render('_price_list', ['model' => $model, 'form' => $form]) ?>

    </div>
    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Save Quote', ['class' => 'btn btn-success']) ?>
    </div>

<?php ActiveForm::end() ?>