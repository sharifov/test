<?php

use common\models\Employee;
use sales\helpers\lead\LeadPreferencesHelper;
use yii\widgets\ActiveForm;

/**
 * @var $leadForm sales\forms\lead\LeadCreateForm
 * @var $form ActiveForm
 */

?>

<div class="sidebar__section">
    <h3 class="sidebar__subtitle">Lead Info</h3>
    <?//= $form->field($leadForm, 'sourceId')->dropDownList($leadForm->listSourceId(), ['prompt' => '---']) ?>

    <?php
        echo $form->field($leadForm, 'sourceId')->widget(\kartik\select2\Select2::class, [
            'data' => $leadForm->listSourceId(),
            'size' => \kartik\select2\Select2::SMALL,
            'options' => ['placeholder' => 'Select market', 'multiple' => false],
            'pluginOptions' => ['allowClear' => true],
        ]);
    ?>

</div>

<div class="sidebar__section">
    <h3 class="sidebar__subtitle">Lead Preferences</h3>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($leadForm->preferences, 'marketPrice')->input('number', ['min' => 0, 'max' => 99000]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($leadForm->preferences, 'clientsBudget')->input('number', ['min' => 0, 'max' => 99000]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($leadForm->preferences, 'numberStops')->dropDownList(LeadPreferencesHelper::listNumberStops(), ['prompt' => '-']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($leadForm, 'delayedCharge')->radioList([false => 'No', true => 'Yes'])?>
        </div>
    </div>
    <?= $form->field($leadForm, 'notesForExperts')->textarea(['rows' => 7]) ?>
</div>
