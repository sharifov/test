<?php

use common\models\SettingCategory;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Setting */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="setting-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-3">

        <?//= $form->field($model, 's_key')->textInput(['maxlength' => true]) ?>

        <?//= $form->field($model, 's_name')->textInput(['maxlength' => true]) ?>

        <?//= $form->field($model, 's_type')->textInput(['maxlength' => true]) ?>

        <?php
            if($model->s_type === \common\models\Setting::TYPE_BOOL) {

                echo $form->field($model, 's_value')->checkbox();//->label($model->s_name);
            } else if($model->s_type === \common\models\Setting::TYPE_INT) {

                echo $form->field($model, 's_value')->input('number');//->label($model->s_name);
            } else if($model->s_type === \common\models\Setting::TYPE_DOUBLE) {

                echo $form->field($model, 's_value')->input('number', ['step' => 0.01]);//->label($model->s_name);
            } else if($model->s_type === \common\models\Setting::TYPE_ARRAY) {


                try {
                    echo $form->field($model, 's_value')->widget(
                        \kdn\yii2\JsonEditor::class,
                        [
                            'clientOptions' => [
                                'modes' => ['code', 'form', 'tree', 'view'], //'text',
                                'mode' => 'tree'
                            ],
                            //'collapseAll' => ['view'],
                            'expandAll' => ['tree', 'form'],
                        ]
                    );
                } catch (Exception $exception) {
                    echo $form->field($model, 's_value')->textarea(['rows' => 5]);//->label($model->s_name);
                }

            }

            else {
                echo $form->field($model, 's_value')->textInput(['maxlength' => true])->label($model->s_name);
            }
        ?>

        <?//= $form->field($model, 's_value')->textInput(['maxlength' => true]) ?>

        <?//= $form->field($model, 's_updated_dt')->textInput() ?>

        <?//= $form->field($model, 's_updated_user_id')->textInput() ?>

        <?= $form->field($model, 's_category_id')->dropDownList(SettingCategory::getList(), ['prompt' => 'Select category', 'style'=>'width: 320px']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save Value', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
