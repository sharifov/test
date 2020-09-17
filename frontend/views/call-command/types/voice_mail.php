<?php

use sales\model\call\entity\callCommand\types\VoiceMail;
use sales\model\call\services\CallCommandTypeService;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var yii\web\View $this */
/* @var yii\widgets\ActiveForm $formType */
/* @var int $typeId */
/* @var VoiceMail $model */
/* @var string $index */

$index = $index ?: '';
$callCommandTypeService = new CallCommandTypeService($model->getTypeId());
$formId = $callCommandTypeService::viewNameFormatting($callCommandTypeService->getTypeName()) . '_command_type_form_' . $index;
?>

    <?php echo Yii::$app->controller->renderPartial('types/_partial_header_form', ['model' => $model]) ?>

    <?php $formType = ActiveForm::begin($callCommandTypeService::configActiveForm($formId, $typeId)); ?>

        <?php echo $formType->field($model, 'sort')->hiddenInput()->label(false) ?>
        <?php echo $formType->field($model, 'typeId')->hiddenInput()->label(false) ?>

        <div class="row">
            <div class="col-md-4">
                <?php echo $formType->field($model, 'vm_id')->input('number', ['min' => 0, 'step' => 1]) ?>
                <?php /* TODO: dropDownList from VoiceMail AR model ? */ ?>
            </div>
        </div>

    <?php $formType::end(); ?>
