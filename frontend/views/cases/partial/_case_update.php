<?php

use frontend\widgets\nestedSets\NestedSetsWidget;
use src\entities\cases\CaseCategory;
use src\model\cases\useCases\cases\updateInfo\UpdateInfoForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model UpdateInfoForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $categoryList [] */

?>
<?php Pjax::begin(['id' => 'pjax-cases-update-form']); ?>
<div class="cases-change-status">
    <?php $form = ActiveForm::begin([
        'action' => ['/cases/ajax-update', 'gid' => $model->getCaseGid()],
        'method' => 'post',
        'options' => ['data-pjax' => true]
    ]); ?>

    <?php
    echo $form->errorSummary($model);
    ?>

    <?= $form->field($model, 'depId')->dropDownList($model->getdepartmentList(), [
        'prompt' => '-',
        'disabled' => !$model->fieldAccess->canEditDepartment(),
        'onchange' => $model->fieldAccess->canEditCategory() ?
            '
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
                ' : '$( "#' . Html::getInputId($model, 'categoryId') . '").prop("disabled", true);'
    ]); ?>




    <?php

    echo $form->field($model, 'categoryId')->widget(NestedSetsWidget::class, [
        'query' => CaseCategory::findNestedSets()->where(['cc_dep_id' => $model->depId ?? null ]),
        'attribute' => 'categoryId',
        'model' => $model,
        'label' => '',
        'allowToSelectEnabled' => true,
        'placeholder' => 'Choose a category',
        'parentCategoryId' => $model->categoryId ?? null,
        'disabled' => !$model->fieldAccess->canEditCategory(),
    ]); ?>

    <?= $form->field($model, 'orderUid')->textInput(['maxlength' => 7]) ?>

    <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 4, 'disabled' => !$model->fieldAccess->canEditDescription()]); ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Update', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php if ($model->fieldAccess->canEditDepartment() && (count($model->getdepartmentList()) === 1)) {
    $keyDepartment = array_key_first($model->getdepartmentList());
    $js = '$( "#' . Html::getInputId($model, 'depId') . '" ).val("' . $keyDepartment . '").change();';
    $this->registerJs($js);
} ?>
<?php Pjax::end(); ?>
