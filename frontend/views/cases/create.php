<?php

use borales\extensions\phoneInput\PhoneInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model sales\forms\cases\CasesCreateByWebForm */

$this->title = 'Create Case';
$this->params['breadcrumbs'][] = ['label' => 'Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="cases-create">
    <div class="x_panel">
        <div class="x_title">
           <h2><i class="fa fa-cube"></i> Create New Case</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: block;">
            <?/*<h1><?= Html::encode($this->title) ?></h1>*/?>
            <?php $form = ActiveForm::begin([
                'enableClientValidation' => false,
                'enableAjaxValidation' => true,
                'validationUrl' => ['/cases/create-validation']
            ]); ?>
            <div class="col-md-4">

                <?php
                    echo $form->errorSummary($model);
                ?>
                <?= $form->field($model, 'projectId')->dropDownList($model->getProjects(), ['prompt' => 'Choose a project']) ?>

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

                <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>



                <div class="form-group text-center">
                    <?= Html::submitButton('<i class="fa fa-plus"></i> Create Case', ['class' => 'btn btn-success']) ?>
                </div>

            </div>
            <?php ActiveForm::end(); ?>
        </div>
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
