<?php

use src\helpers\lead\LeadHelper;
use src\model\lead\useCases\lead\create\LeadCreateForm;
use src\model\lead\useCases\lead\create\LeadManageForm;
use yii\widgets\ActiveForm;

/**
 * @var $leadForm LeadManageForm
 * @var $form ActiveForm
 */

?>

<?php //= $form->field($leadForm, 'sourceId')->dropDownList($leadForm->listSourceId(), ['prompt' => '---']) ?>

<div class="row">
    <div class="col-md-6">
        <?= $form->field($leadForm, 'source')->widget(\kartik\select2\Select2::class, [
            'data' => $leadForm->listSources(),
            'size' => \kartik\select2\Select2::SMALL,
            'options' => ['placeholder' => 'Select market', 'multiple' => false],
            'pluginOptions' => ['allowClear' => true],
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($leadForm, 'depId', [
        ])->dropDownList(LeadHelper::getDepartments(Yii::$app->user)) ?>
    </div>
</div>
