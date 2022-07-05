<?php

use common\models\Currency;
use kartik\select2\Select2;
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
        <?= $form->field($leadForm, 'source')->widget(Select2::class, [
            'data' => $leadForm->listSources(),
            'size' => Select2::SMALL,
            'options' => ['placeholder' => 'Select market', 'multiple' => false],
            'pluginOptions' => ['allowClear' => true],
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($leadForm, 'depId', [
        ])->dropDownList(LeadHelper::getDepartments(Yii::$app->user), [
            'data' => [
                'value' => null,
            ]
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($leadForm->preferences, 'currency')->widget(Select2::class, [
            'data' => Currency::getList(),
            'size' => Select2::SIZE_SMALL
        ]) ?>
    </div>
</div>
