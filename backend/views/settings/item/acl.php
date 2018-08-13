<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/**
 * @var $this yii\web\View
 * @var $model \common\models\GlobalAcl
 */

$idForm = sprintf('%s-ID', $model->formName());

?>

<?php $form = ActiveForm::begin([
    'action' => Yii::$app->urlManager->createUrl(['settings/acl-rule', 'id' => 0]),
    'successCssClass' => '',
    'id' => $idForm,
    'options' => [
        'class' => 'form-inline'
    ]
]) ?>
<?= $form->field($model, 'mask', [
    'options' => [
        'class' => 'form-group'
    ],
    'template' => '{label}: {input}'
])->widget(MaskedInput::className(), [
    'clientOptions' => [
        'alias' => 'ip'
    ],
]) ?>
    <span>&nbsp;</span>
<?= $form->field($model, 'description', [
    'options' => [
        'class' => 'form-group'
    ],
    'template' => '{label}: {input}'
])->textInput() ?>
    <span>&nbsp;</span>
<?= Html::button('Save', [
    'class' => 'btn-success btn',
    'id' => 'submit-btn'
]) ?>
&nbsp;&nbsp;
<?= Html::button('Close', [
    'class' => 'btn-danger btn',
    'id' => 'close-btn'
]) ?>
<?php ActiveForm::end() ?>