<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLineUserAssign\entity\PhoneLineUserAssign */
/* @var $form ActiveForm */
?>

<div class="phone-line-user-assign-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'plus_line_id')->textInput() ?>

        <?= $form->field($model, 'plus_user_id')->textInput() ?>

        <?= $form->field($model, 'plus_allow_in')->checkbox() ?>

        <?= $form->field($model, 'plus_allow_out')->checkbox() ?>

        <?= $form->field($model, 'plus_enabled')->checkbox() ?>

        <?= $form->field($model, 'plus_uvm_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

    <div class="col-md-4">
        <?php

        try {
            echo $form->field($model, 'plus_settings_json')->widget(
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
            echo $form->field($model, 'plus_settings_json')->textarea(['rows' => 6]);
        }

        ?>
    </div>

</div>
