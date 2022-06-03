<?php

use frontend\helpers\PasswordHelper;
use kartik\password\PasswordInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ApiUser */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="api-user-form">

    <div class="card card-default">

        <div class="card-body">

            <div class="panel-body panel-collapse collapse show">
                <?php $form = ActiveForm::begin(); ?>

                <div class="col-md-6">

                    <?= $form->field($model, 'au_name')->textInput(['maxlength' => true]) ?>

                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'au_api_username')
                                ->textInput(['maxlength' => true, 'autocomplete' => 'new-user']) ?>
                        </div>
                        <div class="col-md-8">
                            <?= $form->field($model, 'au_api_password', [
                                'options' => [
                                    'class' => 'form-group'
                                ]
                            ])->widget(PasswordInput::class, [
                                'options' => [
                                    'autocomplete' => 'new-password',
                                ],
                            ])->label(
                                PasswordHelper::getLabelWithTooltip($model, 'au_api_password')
                            ); ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'au_email')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'au_project_id')
                        ->dropDownList(\common\models\Project::getList(), ['prompt' => '---']) ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'au_rate_limit_number')->input('number', ['min' => 0]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'au_rate_limit_reset')->input('number', ['min' => 0]) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'au_enabled')->checkbox() ?>

                    <div class="form-group">
                        <?= Html::submitButton('Save API User', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

</div>
