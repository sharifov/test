<?php

use kdn\yii2\JsonEditor;
use sales\model\clientChat\componentEvent\entity\ClientChatComponentEvent;
use sales\model\clientChat\componentRule\entity\RunnableComponent;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii\bootstrap4\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \sales\model\clientChat\componentEvent\form\ComponentEventCreateForm */
/* @var $form ActiveForm */

$select2Properties = [
    'options' => [
        'placeholder' => 'Select location ...',
        'multiple' => false,
    ],
    'pluginOptions' => [
        'width' => '100%',
        'allowClear' => true,
        'minimumInputLength' => 1,
        'language' => [
            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
        ],
        'ajax' => [
            'url' => ['/airport/get-list'],
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { return {term:params.term}; }'),
        ],
        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        'templateResult' => new JsExpression('formatRepo'),
        'templateSelection' => new JsExpression('function (data) { return data.selection || data.text;}'),
    ]
];
?>

<div class="client-chat-component-event-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-12">
        <?= $form->errorSummary($model) ?>
    </div>

    <div class="col-md-4">


        <?= $form->field($model->componentEvent, 'ccce_chat_channel_id')->dropDownList(ClientChatChannel::getList(), ['prompt' => '---'])->label('Chat Channel') ?>

        <?= $form->field($model->componentEvent, 'ccce_component')->dropDownList(ClientChatComponentEvent::getComponentEventList(), [
            'prompt' => '---'
        ])->label('Component') ?>

        <?= $form->field($model->componentEvent, 'ccce_event_type')->dropDownList(ClientChatComponentEvent::getComponentTypeList(), [
            'prompt' => '---'
        ])->label('Event type') ?>

        <?= $form->field($model->componentEvent, 'ccce_enabled')->checkbox([], false)->label('Enabled') ?>

        <?= $form->field($model->componentEvent, 'ccce_sort_order')->input('number')->label('Sort Order') ?>
    </div>

    <div class="col-md-8">
        <?php

        try {
            echo $form->field($model->componentEvent, 'ccce_component_config')->widget(
                JsonEditor::class,
                [
                    'clientOptions' => [
                        'modes' => ['code', 'form', 'tree', 'view'], //'text',
                        'mode' => 'tree'
                    ],
                    //'collapseAll' => ['view'],
                    'expandAll' => ['tree', 'form'],
                ]
            )->label('Config');
        } catch (Exception $exception) {
            echo $form->field($model->componentEvent, 'ccce_component_config')->textarea(['rows' => 6])->label('Config');
        }

        ?>
    </div>

    <div class="col-md-12" style="margin-top: 20px;">
        <hr>
        <h4>Client Chat Component Rules</h4>

        <?= $form->field($model, 'componentRules')->widget(MultipleInput::class, [
            'max' => 10,
//    'allowEmptyList' => true,
            'id' => 'component_rules_multiple_input',
            'enableError' => true,
            'showGeneralError' => true,
            'columns' => [
                [
                    'name' => 'cccr_value',
                    'title' => 'Value',
                    'value' => static function ($componentRule) {
                        return $componentRule['cccr_value'] ?? '';
                    },
                    'headerOptions' => [
                        'style' => 'width: 130px;',
                    ]
                ],
                [
                    'name' => 'cccr_runnable_component',
                    'title' => 'Runnable Component',
                    'type' => MultipleInputColumn::TYPE_DROPDOWN,
                    'value' => static function ($componentRule) {
                        return $componentRule['cccr_runnable_component'] ?? '';
                    },
                    'items' => RunnableComponent::getListName(),
                    'options' => [
                        'prompt' => '---'
                    ],
                    'headerOptions' => [
                        'style' => 'width: 230px;',
                    ]
                ],
                [
                    'name' => 'cccr_sort_order',
                    'title' => 'Sort Order',
                    'options' => [
                        'type' => 'number'
                    ],
                    'headerOptions' => [
                        'style' => 'width: 130px;',
                    ]
                ],
                [
                    'name' => 'cccr_enabled',
                    'title' => 'Enabled',
                    'type' => MultipleInputColumn::TYPE_CHECKBOX,
                    'headerOptions' => [
                        'style' => 'width: 130px;',
                    ]
                ],
                [
                    'name' => 'cccr_component_config',
                    'type' => JsonEditor::class,
                    'options' => [
                        'clientOptions' => [
                            'modes' => ['code', 'form', 'tree', 'view'], //'text',
                            'mode' => 'tree'
                        ],
                        //'collapseAll' => ['view'],
                        'expandAll' => ['tree', 'form'],
                    ]
                ]
            ]
        ])->label(false) ?>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
