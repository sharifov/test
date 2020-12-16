<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatVisitorData\entity\ClientChatVisitorData */
/* @var $form ActiveForm */
?>

<div class="client-chat-visitor-data-form">

    <?php $form = ActiveForm::begin(); ?>
        <div class="col-md-4">

            <?= $form->field($model, 'cvd_country')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'cvd_region')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'cvd_city')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'cvd_latitude')->textInput() ?>

            <?= $form->field($model, 'cvd_longitude')->textInput() ?>

            <?= $form->field($model, 'cvd_url')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'cvd_title')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'cvd_referrer')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'cvd_timezone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'cvd_local_time')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'cvd_created_dt')->textInput() ?>

            <?= $form->field($model, 'cvd_updated_dt')->textInput() ?>

            <?= $form->field($model, 'cvd_visitor_rc_id')->textInput(['maxlength' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

        </div>

        <div class="col-md-6">
            <?php
            try {
                echo $form->field($model, 'cvd_data')->widget(
                    \kdn\yii2\JsonEditor::class,
                    [
                        'clientOptions' => [
                            'modes' => ['code', 'form', 'tree', 'view'], //'text',
                            'mode' => $model->isNewRecord ? 'code' : 'form'
                        ],
                        //'collapseAll' => ['view'],
                        'expandAll' => ['tree', 'form'],
                    ]
                );
            } catch (Exception $exception) {
                echo $form->field($model, 'cvd_data')->textarea(['rows' => 6]);
            }
            ?>
        </div>
    <?php ActiveForm::end(); ?>

</div>
