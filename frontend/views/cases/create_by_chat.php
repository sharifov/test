<?php

use frontend\widgets\nestedSets\NestedSetsWidget;
use src\entities\cases\CaseCategory;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model src\forms\cases\CasesCreateByChatForm */

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
                                const nestedSetsSelect = $("#categoryId");
                                nestedSetsSelect.select2ToTree({"placeholder" : "Searching..."}).attr("disabled", "disabled");
                              
                                $.get( "' . Url::to(['/cases/get-nested-categories']) . '", { id: $(this).val() } )
                                    .done(function( data ) {
                                        data = $.parseJSON(data);
                                        const nestedSetsSelect = $("#categoryId");
                                        nestedSetsSelect.select2("destroy").empty();
                                        nestedSetsSelect.prepend(\'<option selected=""></option>\');
                                        
                                        nestedSetsSelect.select2ToTree({
                                            treeData: {dataArr: data},
                                              maximumSelectionLength: 3,
                                            allowClear: true,
                                            placeholder: "Choose a category",
                                            templateResult: formatState,
                                            templateSelection: formatState       
                                                                             
                                        });  
                                        function formatState (state) {
                                            if (!state.disabled) {
                                              return state.text;
                                            }
                                            return $(\'<span><i class="fa fa-lock"></i> \' + state.text + \'</span>\');
                                        }
                                        nestedSetsSelect.removeAttr("disabled");
                                        nestedSetsSelect.trigger(\'change.select2\');
                                    }
                                );
                                '
                        ]) ?>

                        <?= $form->field($model, 'categoryId')->widget(NestedSetsWidget::class, [
                            'query' => CaseCategory::findNestedSets()->where(['cc_dep_id' => $model->depId ?? null]),
                            'attribute' => 'categoryId',
                            'model' => $model,
                            'label' => '',
                            'allowToSelectEnabled' => true,
                            'placeholder' => 'Choose a category'
                        ]); ?>



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
