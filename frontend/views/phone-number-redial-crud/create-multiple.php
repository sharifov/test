<?php

use common\models\Project;
use unclead\multipleinput\MultipleInput;
use yii\bootstrap\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\JsExpression;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model \src\model\phoneNumberRedial\useCase\createMultiple\CreateMultipleForm */

$this->title = 'Create Phone Number Redial';
$this->params['breadcrumbs'][] = ['label' => 'Phone Number Redials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$select2Properties = [
    'options' => [
        'placeholder' => 'Select phone number ...',
        'multiple' => false,
    ],
    'pluginOptions' => [
//      'width' => '100%',
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
<div class="phone-number-redial-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="phone-number-redial-form">

        <div class="col-md-2">

            <?php Pjax::begin(['enablePushState' => false, 'enableReplaceState' => false]) ?>

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->errorSummary($model) ?>

            <?= $form->field($model, 'projectId')->widget(\src\widgets\ProjectSelect2Widget::class, [
                'pluginOptions' => [
                    'allowClear' => true,
                    'templateSelection' => new JsExpression('function (data) { return data.text || data.selection;}'),
                ],
                'initValueText' => $model->projectId ? Project::findOne($model->projectId)->name : null
            ]) ?>

            <?= $form->field($model, 'phonePattern')->widget(MultipleInput::class, [
                'enableError' => true,
                'showGeneralError' => true
            ]) ?>

            <?= $form->field($model, 'phoneNumber')->widget(MultipleInput::class, [
                'enableError' => true,
                'showGeneralError' => true,
                'columns' => [
                    [
                        'name' => 'phoneNumber',
                        'type' => \src\widgets\PhoneSelect2Widget::class,
                        'title' => false,
                        'value' => static function ($phoneId) {
                            return \src\model\phoneList\entity\PhoneList::findOne((int)$phoneId)->pl_phone_number ?? null;
                        },
                        'headerOptions' => [
                        ]
                    ],
                ]
            ])->label('Phone Number') ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'enabled')->checkbox() ?>

            <?= $form->field($model, 'priority')->input('number') ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                <?= Html::a('cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <?php Pjax::end() ?>

        </div>

    </div>

</div>
