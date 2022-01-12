<?php

use src\model\clientChat\componentEvent\entity\ClientChatComponentEvent;
use src\model\clientChatChannel\entity\ClientChatChannel;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\clientChat\componentEvent\entity\ClientChatComponentEvent */
/* @var $form ActiveForm */
?>

<div class="client-chat-component-event-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ccce_chat_channel_id')->dropDownList(ClientChatChannel::getList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'ccce_component')->dropDownList(ClientChatComponentEvent::getComponentEventList(), [
            'prompt' => '---'
        ]) ?>

        <?= $form->field($model, 'ccce_event_type')->dropDownList(ClientChatComponentEvent::getComponentTypeList(), [
            'prompt' => '---'
        ]) ?>

        <?= $form->field($model, 'ccce_enabled')->checkbox() ?>

        <?= $form->field($model, 'ccce_sort_order')->input('number') ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>


    </div>

    <div class="col-md-8">
        <?php

        try {
            echo $form->field($model, 'ccce_component_config')->widget(
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
            echo $form->field($model, 'ccce_component_config')->textarea(['rows' => 6]);
        }

        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
