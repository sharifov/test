<?php

use borales\extensions\phoneInput\PhoneInput;
//use frontend\extensions\PhoneInput;
use frontend\widgets\nestedSets\NestedSetsWidget;
use src\entities\cases\CaseCategory;
use src\forms\cases\CaseCategoryManageForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model src\forms\cases\CasesCreateByWebForm */

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
            <?php /*<h1><?= Html::encode($this->title) ?></h1>*/?>
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

                <?= $form->field($model, 'categoryId', ['enableAjaxValidation' => true])->widget(NestedSetsWidget::class, [
                    'query' => CaseCategory::findNestedSets()->where(['cc_dep_id' => $model->getDepartmentId()]),
                    'attribute' => 'categoryId',
                    'model' => $model,
                    'label' => '',
                    'allowToSelectEnabled' => true,
                    'placeholder' => 'Choose a category'
                ]); ?>

                <?php
                    // $form->field($model, 'categoryId')->dropDownList([], ['prompt' => 'Choose a category']) ?>

                <?= $form->field($model, 'sourceTypeId')->dropDownList($model->getSourceTypeList(), ['prompt' => 'Choose a source type']) ?>

                <?= $form->field($model, 'orderUid')->textInput(['maxlength' => 7]) ?>

                <?= $form->field($model, 'clientPhone')->widget(PhoneInput::class, [
                    'name' => 'phone',
                    'jsOptions' => [
                        'nationalMode' => false,
                        'preferredCountries' => ['us'],
                        'customContainer' => 'intl-tel-input'
                    ]
                ]) ?>
                <div class="phone-notify"></div>

                <?= $form->field($model, 'clientEmail')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                <?= $form->field($model, 'saleId')->hiddenInput()->label(false) ?>



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

$phoneFieldId = Html::getInputId($model, 'clientPhone');
$js = <<<JS
    $(document).ready( function () {
        $('#$phoneFieldId').on('keyup', delay(function (e) {
            $('.phone-notify').html('');
            $.post('/cases/check-phone-for-existence', {clientPhone: $(this).val()}).done( function (response) {
                    if (response.clientPhoneResponse) {
                        $('.phone-notify').html(response.clientPhoneResponse);
                    }
            });
        }, 1200));
        
        function delay(callback, ms) {
              var timer = 0;
              
              return function() {
                    var context = this, args = arguments;
                    
                    clearTimeout(timer);
                    timer = setTimeout(function () {
                      callback.apply(context, args);
                    }, ms || 0);
              };
        }
        
        let department = $('#casescreatebywebform-depid');
        if(department.val() !== '') {
            setTimeout(function () {
                department.trigger('change');
            }, 100);
        }
    });
JS;
$this->registerJs($js);