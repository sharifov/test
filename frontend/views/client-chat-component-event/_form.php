<?php

use kartik\select2\Select2;
use kdn\yii2\JsonEditor;
use sales\model\clientChat\componentEvent\entity\ClientChatComponentEvent;
use sales\model\clientChat\componentRule\entity\RunnableComponent;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii\bootstrap4\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model \sales\model\clientChat\componentEvent\form\ComponentEventCreateForm */
/* @var $form ActiveForm */

?>

<div class="client-chat-component-event-form">
    <?php $pjax = Pjax::begin([
        'id' => 'pjax-cc-component-event-creation',
        'timeout' => 5000,
        'enablePushState' => false,
        'enableReplaceState' => false,
        'clientOptions' => ['async' => false]
    ])?>

    <?php $form = ActiveForm::begin([
        'id' => 'cc-component-event-creation',
        'options' => [
            'data-pjax' => 1
        ],
        'enableClientValidation' => false
    ]); ?>

    <div class="col-md-12">
        <?= $form->errorSummary($model) ?>
    </div>

    <div class="col-md-4">

        <?= $form->field($model->componentEvent, 'ccce_component')->widget(Select2::class, [
            'data' => ClientChatComponentEvent::getComponentEventList(),
            'size' => Select2::SMALL,
            'options' => [
                'id' => 'ccce_component',
                'prompt' => '---'
            ],
            'pluginOptions' => ['allowClear' => true],
        ])->label('Component') ?>

        <?= $form->field($model, 'component_event_changed')->hiddenInput([
            'id' => 'component_event_changed'
        ])->label(false) ?>

        <?= $form->field($model, 'pjaxReload')->hiddenInput(['id' => 'pjaxReload'])->label(false) ?>

        <?= $form->field($model->componentEvent, 'ccce_event_type')->widget(Select2::class, [
            'options' => [
                'prompt' => '---'
            ],
            'size' => Select2::SMALL,
            'data' => ClientChatComponentEvent::getComponentTypeList(),
            'pluginOptions' => ['allowClear' => true],
        ])->label('Event type') ?>

        <?= $form->field($model->componentEvent, 'ccce_chat_channel_id')->widget(Select2::class, [
            'data' => ClientChatChannel::getList(),
            'options' => [
                'prompt' => '---'
            ],
            'size' => Select2::SMALL,
            'pluginOptions' => ['allowClear' => true],
        ])->label('Chat Channel') ?>

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
            )->label('Component Event config <i class="fa fa-info" title="The config will be transferred to the component at the time of its execution" data-toggle="tooltip"></i>');
        } catch (Exception $exception) {
            echo $form->field($model->componentEvent, 'ccce_component_config')->textarea(['rows' => 6])->label('Component Event config');
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
                    'type' => Select2::class,
                    'value' => static function ($componentRule) {
                        return $componentRule['cccr_runnable_component'] ?? '';
                    },
                    'options' => [
                        'data' => RunnableComponent::getListName(),
                        'options' => [
                          'prompt' => '---',
                          'class' => 'cccr_runnable_component'
                        ],
                        'class' => 'cccr_runnable_component',
                        'pluginOptions' => ['allowClear' => true],
                        'size' => Select2::SMALL,
                    ],
                    'headerOptions' => [
                        'style' => 'width: 230px;',
                    ]
                ],
                [
                    'name' => 'cccr_runnable_component_changed',
                    'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,
                    'options' => [
                        'class' => 'cccr_runnable_component_changed'
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
                    'title' => 'Rules Config <i class="fa fa-info" title="The config will be transferred to the component at the time of its execution" data-toggle="tooltip"></i>',
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

    <?php Pjax::end(); ?>
</div>

<?php
$typeInputId = Html::getInputId($model, 'ccce_component');
$js = <<<JS
(function () {
    $(document).on('change', '#ccce_component', function(e) {
        $('#component_event_changed').val(1);
        $('#pjaxReload').val(1);
        $('#cc-component-event-creation').submit();
    });
    
    $(document).on('change', '.cccr_runnable_component', function (e) {
        $(this).closest('tr').find('.cccr_runnable_component_changed').val(1);
        $('#pjaxReload').val(1); 
        $('#cc-component-event-creation').submit();
    });
})();
JS;
$this->registerJs($js);

