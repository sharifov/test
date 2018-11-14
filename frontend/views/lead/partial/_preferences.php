<?php

use yii\widgets\ActiveForm;
use frontend\models\LeadForm;
use common\models\ProjectEmployeeAccess;
use common\models\ClientPhone;
use yii\helpers\Html;

/**
 * @var $this \yii\web\View
 * @var $formPreferences ActiveForm
 * @var $leadForm LeadForm
 * @var $nr integer
 * @var $newPhone ClientPhone
 */

$formId = sprintf('%s-form', $leadForm->getLeadPreferences()->formName());
?>

<?php $formPreferences = ActiveForm::begin([
    //'enableClientValidation' => false,
    'id' => $formId
]); ?>
<div class="sidebar__section">
	<?php if ($leadForm->getLead()->isNewRecord) : ?>
    <h3 class="sidebar__subtitle">Lead Info</h3>
    <?= $formPreferences->field($leadForm->getLead(), 'source_id')
                ->dropDownList(ProjectEmployeeAccess::getAllSourceByEmployee(), [
                    'prompt' => 'Select'
                ])->label('Marketing Info:') ?>
    <?= $formPreferences->field($leadForm->getLead(), 'uid')
                ->textInput([
                    'class' => 'form-control lead-form-input-element'
                ]) ?>
    <?php endif; ?>
</div>
<div class="sidebar__section">
    <h3 class="sidebar__subtitle">Client Preferences</h3>
    <div class="row">
        <div class="col-md-6">
            <?= $formPreferences->field($leadForm->getLeadPreferences(), 'market_price')
                ->textInput([
                    'class' => 'form-control lead-form-input-element'
                ]) ?>
        </div>
        <div class="col-md-6">
            <?= $formPreferences->field($leadForm->getLeadPreferences(), 'clients_budget')
                ->textInput([
                    'class' => 'form-control lead-form-input-element'
                ]) ?>
        </div>
    </div>
    <?= $formPreferences->field($leadForm->getLeadPreferences(), 'number_stops')
        ->textInput([
            'class' => 'form-control lead-form-input-element',
            'type' => 'number',
            'min' => 0,
        ]) ?>
    <?= $formPreferences->field($leadForm->getLead(), 'notes_for_experts')
        ->textarea([
            'rows' => 7,
            'class' => 'form-control'
        ]) ?>
</div>
<?php ActiveForm::end(); ?>