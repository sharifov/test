<?php

use common\models\Language;
use sales\model\call\entity\callCommand\types\Gather;
use sales\model\call\services\CallCommandTypeService;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var yii\web\View $this */
/* @var yii\widgets\ActiveForm $formType */
/* @var int $typeId */
/* @var Gather $model */
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
                <?php echo $formType->field($model, 'type_id')->dropDownList(Gather::TYPE_LIST) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php echo $formType->field($model, 'action')->textInput(['maxlength' => true])  ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php echo $formType->field($model, 'finishOnKey')->dropDownList(Gather::FINISH_ON_KEYS) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php echo $formType->field($model, 'hints')->textInput(['maxlength' => true])  ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php echo $formType->field($model, 'input')->dropDownList(Gather::INPUT_LIST) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php echo $formType->field($model, 'language')->dropDownList(Language::getLanguages()) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php echo $formType->field($model, 'method')->dropDownList(Gather::METHOD_LIST) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php echo $formType->field($model, 'numDigits')->input('number', ['min' => 1, 'step' => 1]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php echo $formType->field($model, 'partialResultCallback')->textInput(['maxlength' => true])  ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php echo $formType->field($model, 'method')->dropDownList(Gather::METHOD_LIST) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php echo $formType->field($model, 'profanityFilter')->dropDownList([false => 'No', true => 'Yes']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php echo $formType->field($model, 'speechTimeout')->textInput(['maxlength' => true])  ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php echo $formType->field($model, 'timeout')->input('number', ['min' => 1, 'step' => 1]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php echo $formType->field($model, 'speechModel')->dropDownList(Gather::SPEECH_MODEL_LIST) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php echo $formType->field($model, 'enhanced')->dropDownList([false => 'No', true => 'Yes']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php echo $formType->field($model, 'actionOnEmptyResult')->dropDownList([false => 'No', true => 'Yes']) ?>
            </div>
        </div>

    <?php $formType::end(); ?>

