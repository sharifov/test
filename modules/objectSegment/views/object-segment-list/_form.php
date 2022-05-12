<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model \modules\objectSegment\src\forms\ObjectSegmentListForm */
/* @var $osl \modules\objectSegment\src\entities\ObjectSegmentList*/
/* @var $form yii\widgets\ActiveForm */

\frontend\assets\QueryBuilderAsset::register($this);

?>
<style>
    .rules-group-container {width: 100%}
    .rule-value-container {display:inline-flex!important;}
</style>
<div class="abac-policy-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model) ?>

    <div class="row">
        <div class="col-md-5">
            <?php if ($osl->isNewRecord) : ?>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'osl_ost_id', [
                    ])->widget(Select2::class, [
                        'data' => $model->getObjectTypeList(),
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select Type', 'multiple' => false],
                        'pluginOptions' => ['allowClear' => true],
                    ]) ?>
                </div>
                <div class="col-md-6">

                </div>
            </div>
            <?php else : ?>
                <?php echo $form->field($model, 'osl_ost_id', [
                ])
                    ->hiddenInput()
                    ->label(false);
                ?>
            <?php endif; ?>


            <?= $form->field($model, 'osl_key')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'osl_title')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'osl_description')->textarea(['maxlength' => true]) ?>
            <?= $form->field($model, 'osl_enabled')->checkbox() ?>

        </div>


    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-save"></i> Save Policy', ['class' => 'btn btn-success', 'id' => 'btn-submit']) ?>
            </div>
        </div>
    </div>


    <div class="row">
    </div>


    <?php ActiveForm::end(); ?>

</div>