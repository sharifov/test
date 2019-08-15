<?php

use borales\extensions\phoneInput\PhoneInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model sales\forms\cases\CasesCreateByWebForm */

$this->title = 'Create Case';
$this->params['breadcrumbs'][] = ['label' => 'Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
    <div class="cases-create">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="cases-form">

            <?php $form = ActiveForm::begin([
                'enableClientValidation' => false,
                'enableAjaxValidation' => true,
                'validationUrl' => ['/cases/create-validation']
            ]); ?>

            <div class="col-md-4">

                <?= $form->field($model, 'projectId')->dropDownList($model->getProjects(), ['prompt' => 'Choose a project']) ?>

                <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                <?= $form->field($model, 'depId')->dropDownList($model->getDepartments(), [
                    'prompt' => 'Choose a department',
                    'onchange' => '
                        $.get( "' . Url::to(['/cases/get-categories']) . '", { id: $(this).val() } )
                            .done(function( data ) {
                                $( "#' . Html::getInputId($model, 'category') . '" ).html( data );
                            }
                        );
                    '
                ]) ?>

                <?= $form->field($model, 'category')->dropDownList([], ['prompt' => 'Choose a category']) ?>

                <?= $form->field($model, 'clientPhone')->widget(PhoneInput::class, [
                    'name' => 'phone',
                    'jsOptions' => [
                        'nationalMode' => false,
                        'preferredCountries' => ['us'],
                    ]
                ]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                </div>

            </div>

            <?php ActiveForm::end(); ?>

        </div>

    </div>

<?php
if (count($model->getProjects()) === 1) {
    $keyProject = array_key_first($model->getProjects());
    $js = '$( "#' . Html::getInputId($model, 'projectId') . '" ).val("' . $keyProject . '").change();';
    $this->registerJs($js);
}
if (count($model->getDepartments()) === 1) {
    $keyDepartment = array_key_first($model->getDepartments());
    $js = '$( "#' . Html::getInputId($model, 'depId') . '" ).val("' . $keyDepartment . '").change();';
    $this->registerJs($js);
}
