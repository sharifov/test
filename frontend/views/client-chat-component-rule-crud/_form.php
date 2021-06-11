<?php

use sales\model\clientChat\componentRule\entity\RunnableComponent;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\componentRule\entity\ClientChatComponentRule */
/* @var $form ActiveForm */
?>

<div class="client-chat-component-rule-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cccr_component_event_id')->input('number') ?>

        <?= $form->field($model, 'cccr_value')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cccr_runnable_component')->dropDownList(RunnableComponent::getListName(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'cccr_sort_order')->textInput() ?>

        <?= $form->field($model, 'cccr_enabled')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

    <div class="col-md-8">
        <?php

        try {
            echo $form->field($model, 'cccr_component_config')->widget(
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
            echo $form->field($model, 'cccr_component_config')->textarea(['rows' => 6]);
        }

        ?>
    </div>

</div>
