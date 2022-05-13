<?php

use yii\bootstrap4\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\phoneNumberRedial\entity\PhoneNumberRedial */
/* @var $form ActiveForm */
?>

<div class="phone-number-redial-form">

    <div class="col-md-4">

        <?php \yii\widgets\Pjax::begin(['enablePushState' => false, 'enableReplaceState' => false]) ?>

          <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'pnr_project_id')->widget(\src\widgets\ProjectSelect2Widget::class, [
                'pluginOptions' => [
                    'allowClear' => true,
                    'templateSelection' => new JsExpression('function (data) { return data.text || data.selection;}'),
                ],
                'initValueText' => $model->pnr_project_id ? $model->pnrProject->name : null
            ]) ?>

            <?= $form->field($model, 'pnr_phone_pattern')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'pnr_pl_id')->input('number') ?>

            <?= $form->field($model, 'pnr_name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'pnr_enabled')->checkbox() ?>

            <?= $form->field($model, 'pnr_priority')->input('number') ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                <?= Html::a('cancel', $model->isNewRecord ? Yii::$app->getUser()->getReturnUrl('index') : ['view', 'pnr_id' => $model->pnr_id], ['class' => 'btn btn-secondary']) ?>
            </div>

          <?php ActiveForm::end(); ?>

        <?php \yii\widgets\Pjax::end() ?>

    </div>

</div>
