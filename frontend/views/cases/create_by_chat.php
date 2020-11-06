<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model sales\forms\cases\CasesCreateByChatForm */

$this->title = 'Create Case';
$this->params['breadcrumbs'][] = ['label' => 'Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
    <div class="cases-create">
        <div class="x_panel">
            <div class="x_content" style="display: block;">
                <?php Pjax::begin([
                    'id' => '_create_case_by_chat',
                    'timeout' => 2000,
                    'enablePushState' => false,
                    'enableReplaceState' => false
                ]); ?>
                    <?php $form = ActiveForm::begin([
                        'id' => $model->formName() . '-form',
                        'enableClientValidation' => true,
                        'options' => [
                            'data-pjax' => 1
                        ],
                    ]); ?>
                    <div class="col-md-12">

                        <?php
                        echo $form->errorSummary($model);
                        ?>
                        <?= $form->field($model, 'projectId')->dropDownList($model->getProjects(), ['prompt' => 'Choose a project']) ?>

                        <?= $form->field($model, 'depId')->dropDownList($model->getDepartments(), [
                            'prompt' => 'Choose a department',
                            'onchange' => '
                            $.get( "' . Url::to(['/cases/get-categories']) . '", { id: $(this).val() } )
                                .done(function( data ) {
                                    $( "#' . Html::getInputId($model, 'categoryId') . '" ).html( data );
                                }
                            );
                        '
                        ]) ?>

                        <?= $form->field($model, 'categoryId')->dropDownList($model->getCategories(), ['prompt' => 'Choose a category']) ?>

                        <?= $form->field($model, 'sourceTypeId')->hiddenInput()->label(false) ?>

                        <?= $form->field($model, 'orderUid')->textInput(['maxlength' => 7]) ?>

                        <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                        <div class="form-group text-center">
                            <?= Html::submitButton('<i class="fa fa-plus"> </i> Create Case', ['class' => 'btn btn-success']) ?>
                        </div>

                    </div>
                    <?php ActiveForm::end(); ?>
                <?php Pjax::end(); ?>
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
