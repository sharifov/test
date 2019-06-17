<?php

use yii\widgets\ActiveForm;

/**
 * @var $leadForm sales\forms\lead\LeadCreateForm
 * @var $form ActiveForm
 */

?>

<div class="sidebar__section">
    <h3 class="sidebar__subtitle">Lead Info</h3>
    <?= $form->field($leadForm, 'sourceId')->dropDownList($leadForm->listSourceId(), ['prompt' => 'Select']) ?>
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
            <?= $form->field($leadForm->preferences, 'numberStops')->dropDownList($leadForm->preferences::listNumberStops(), ['prompt' => '-']) ?>
        </div>
    </div>
    <?= $form->field($leadForm, 'notesForExperts')->textarea(['rows' => 7]) ?>
</div>
