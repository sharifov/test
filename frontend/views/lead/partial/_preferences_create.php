<?php

use common\models\Employee;
use sales\helpers\lead\LeadPreferencesHelper;
use yii\widgets\ActiveForm;

/**
 * @var $leadForm sales\forms\lead\LeadCreateForm
 * @var $form ActiveForm
 */

?>

    <?php //= $form->field($leadForm, 'sourceId')->dropDownList($leadForm->listSourceId(), ['prompt' => '---']) ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($leadForm, 'sourceId')->widget(\kartik\select2\Select2::class, [
                    'data' => $leadForm->listSources(),
                    'size' => \kartik\select2\Select2::SMALL,
                    'options' => ['placeholder' => 'Select market', 'multiple' => false],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
        </div>
        <div class="col-md-2">
			<?= $form->field($leadForm->preferences, 'marketPrice')->input('number', ['min' => 0, 'max' => 99000]) ?>
        </div>
        <div class="col-md-2">
			<?= $form->field($leadForm->preferences, 'clientsBudget')->input('number', ['min' => 0, 'max' => 99000]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($leadForm->preferences, 'numberStops')->dropDownList(LeadPreferencesHelper::listNumberStops(), ['prompt' => '-']) ?>
        </div>
        <div class="col-md-3">
            <div class="d-flex flex-direction-column align-content-end align-items-end" style="height: 100%;">
                <?= $form->field($leadForm, 'delayedCharge')->checkbox()?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($leadForm, 'notesForExperts')->textarea(['rows' => 7]) ?>
        </div>
    </div>
