<?php

use kartik\editable\Editable;
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
    <?/*= $formPreferences->field($leadForm->getLead(), 'uid')
                ->textInput([
                    'class' => 'form-control lead-form-input-element'
                ])*/ ?>
    <?php endif; ?>
</div>
<div class="sidebar__section">
    <h3 class="sidebar__subtitle">Lead Preferences</h3>
    <div class="row">
        <div class="col-md-4">
            <?= $formPreferences->field($leadForm->getLeadPreferences(), 'market_price')->input('number', ['min' => 0, 'max' => 99000])->label('Market, $') ?>
        </div>
        <div class="col-md-4">
            <?= $formPreferences->field($leadForm->getLeadPreferences(), 'clients_budget')->input('number', ['min' => 0, 'max' => 99000])->label('Budget, $') ?>
        </div>
        <div class="col-md-3">
            <?/*= $formPreferences->field($leadForm->getLeadPreferences(), 'number_stops')
            ->textInput([
                'class' => 'form-control lead-form-input-element',
                'type' => 'number',
                'min' => 0,
            ])*/ ?>

            <?= $formPreferences->field($leadForm->getLeadPreferences(), 'number_stops')->dropDownList(array_combine(range(0, 7), range(0, 7)), ['prompt' => '-'])->label('Stops') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $formPreferences->field($leadForm->getLead(), 'l_delayed_charge')->radioList([false => 'No', true => 'Yes'])?>
        </div>
    </div>

    <?php if($leadForm->getLead()->isNewRecord): ?>
        <?php echo $formPreferences->field($leadForm->getLead(), 'notes_for_experts')->textarea(['rows' => 7, 'id' => 'lead-notes_for_experts']); ?>
    <?php endif; ?>

</div>

    <?php ActiveForm::end(); ?>

<style>
    .kv-editable-content {
        width: 100%;
    }
    .kv-editable-content .panel-body {
        padding: 5px;
    }
</style>

<div class="sidebar__section">
    <h3 class="sidebar__subtitle">Notes from Client</h3>
    <div class="row" style="background: #f0f3f8; padding: 5px">

        <?php

        if(!$leadForm->getLead()->isNewRecord) {
            $name = 'notes_for_experts'; //[' . $leadForm->getLead()->id . ']';

            echo Editable::widget([
                'name' => $name,

                'asPopover' => false,
                //'asPopover' => true,

                'inlineSettings' => [
                    'templateBefore' => Editable::INLINE_BEFORE_2,
                    'templateAfter' =>  Editable::INLINE_AFTER_2
                ],

                'displayValue' => nl2br(\yii\helpers\Html::encode($leadForm->getLead()->notes_for_experts)),
                'format' => Editable::FORMAT_BUTTON,
                'inputType' => Editable::INPUT_TEXTAREA,
                'value' => $leadForm->getLead()->notes_for_experts,
                'header' => 'notes for Experts',
                'submitOnEnter' => false,
                /*'formOptions'=>[
                    'action'=>\yii\helpers\Url::to(['lead/view', 'gid' => $leadForm->getLead()->gid]),
                ],*/
                // 'valueIfNull' => '---',
                'size' => \kartik\popover\PopoverX::SIZE_LARGE,
                'options' => ['class' => 'form-control', 'rows' => 7, 'placeholder' => 'Enter notes...', 'id' => 'lead-notes_for_experts']
            ]);


        }
        ?>
    </div>
</div>
