<?php

use common\models\Employee;
use sales\model\call\entity\callCommand\CallCommand;
use sales\model\call\entity\callCommand\types\CommandList;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\CallCommand */
/* @var $form yii\widgets\ActiveForm */
/* @var string $typeForm */

$typeForm = $typeForm ?? '';
?>

<div class="call-command-form">

    <?php $form = ActiveForm::begin(['id' => 'command_form']); ?>

    <div class="row">
        <div class="col-md-4">

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'ccom_parent_id')->input('number', ['min' => 0, 'step' => 1]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'ccom_project_id')->dropDownList(\common\models\Project::getList(), ['prompt' => '-']) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'ccom_lang_id')->dropDownList(\common\models\Language::getLanguages(), ['prompt' => '-']) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'ccom_type_id')->dropDownList(CallCommand::getTypeList(),
                        ['prompt' => '---', 'id' => 'callCommandTypeId']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'ccom_user_id')->dropDownList(Employee::getList(), ['prompt' => '---'])?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'ccom_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'ccom_sort_order')->input('number', ['min' => 0, 'step' => 1]) ?>
                </div>
            </div>

                <?= $form->field($model, 'ccom_params_json')->textarea([
                    'style' => 'display:none;', 'id' => 'params_json'
                ])->label(false) ?>

            <div class="row">
                <div class="col-md-12">
                    <div id="command_list_form_box"></div>
                </div>
            </div>

        </div>

        <div class="col-md-4" id="type_form_box" style="padding-left: 30px;"></div>

        <div class="col-md-4" id="sub_form_box" style="padding-left: 30px;"></div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-save"></i> Save Command', ['class' => 'btn btn-success', 'id' => 'save_command']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$typeCommandList = CallCommand::TYPE_COMMAND_LIST;
$commandListFormName = (new CommandList())->formName();

$js = <<<JS

    var typeCommandListId = $typeCommandList;
    var simpleFormAjaxSend = 0;
    let saveBtnLoaderContent = '<span class="spinner-border spinner-border-sm"></span> Loading'; 
    let saveBtnContent = '<i class="fa fa-save"></i> Save Command';
    
    $(document).on('click', '#save_command', function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        let btn = $(this);
        btn.html(saveBtnLoaderContent).prop("disabled", true);  
                
        let typeObj = $('#callCommandTypeId');
        let typeId = parseInt(typeObj.val(), 10);
        
        if (!Number.isInteger(typeId)) {
            $('#command_form').yiiActiveForm('validateAttribute', 'callcommand-ccom_type_id');
            btn.html(saveBtnContent).prop("disabled", false);
            return false;
        }
        
        if (typeId === typeCommandListId) {
            let formCommandListObj = $('#$commandListFormName');
            
            $.ajax({
                url: '/call-command/validate-command-list-form/?type_id=' + typeCommandListId,
                type: 'POST',
                data: formCommandListObj.serializeArray(),
                dataType: 'json'    
            })
            .done(function(dataResponse) {                
                $.each(dataResponse, function(keyEl, msgs) {

                    let splitKeyEl = keyEl.split('-');
                    splitKeyEl[0] = splitKeyEl[0] + '-multipleformdata';
                    let elementId = splitKeyEl.join('-');
        
                    if (msgs.length) {
                        var message = msgs.join(',');
                        $('#' + elementId).addClass('has-error').prop('title', message);
                    }
                });
            })
            .fail(function(error) {
                console.log(error);        
            });                         
            
            if (formCommandListObj.find('.has-error').length) {
                btn.html(saveBtnContent).prop("disabled", false);
                return false;
            } else {
                let subFormBoxObj = $('#type_form_box');
                
                subFormBoxObj.find('form').each(function() {                     
                    let subForm = $(this);                    
                    let dataForm = subForm.data("yiiActiveForm");
                        
                    $.each(dataForm.attributes, function() {
                        this.status = 3;
                    });
                    subForm.yiiActiveForm('data').submitting = false;
                    subForm.yiiActiveForm("validate");
                })
                .promise()
                .done( function() { 
                    if (subFormBoxObj.find('.has-error').length) {
                        btn.html(saveBtnContent).prop("disabled", false);                    
                        return false;
                    } else {
                    
                        let dataSubForms = [];
                        subFormBoxObj.find('form').each(function() {
                            dataSubForms.push(mappingFormData($(this)));    
                        });
                        
                        $('#params_json').val(JSON.stringify(dataSubForms));                         
                        $('#command_form').submit();                   
                    }  
                });  
                btn.html(saveBtnContent).prop("disabled", false);              
                return false; 
            }  
                        
        } else {
        
            let simpleForm = $("#type_form_box").find('form:first');
            $('#params_json').val(JSON.stringify(mappingFormData(simpleForm)));
            let dataSimpleForm = simpleForm.data("yiiActiveForm");
                
            $.each(dataSimpleForm.attributes, function() {
                this.status = 3;
            });
            
            simpleForm.yiiActiveForm('data').submitting = false;
            simpleForm.yiiActiveForm("validate");
                        
            simpleForm.on('ajaxComplete', function(event, jqXHR, textStatus) {
                if (simpleFormAjaxSend === 1) {
                    btn.html(saveBtnContent).prop("disabled", false);
                    return false;
                }
                simpleFormAjaxSend = 1;                
                if (textStatus === 'success') {
                   $('#command_form').submit(); 
                }
            });
            
            if (simpleForm.find('.has-error').length === 0) {
                $('#command_form').submit(); 
            } 
            btn.html(saveBtnContent).prop("disabled", false);           
            return false;             
        } 
        
        $('#command_form').submit();
    });
        
    $(document).on('change', '#callCommandTypeId', function() {
        
        let typeObj = $(this);
        let typeId = parseInt(typeObj.val(), 10);
        let subFormBoxObj = $('#type_form_box');
        let prevType = typeObj.data('prev_type');
        
        if (!Number.isInteger(typeId)) {
            return false;
        }
                
        if (prevType === typeCommandListId) {
            if(!confirm('Are you sure you want to change the type? Current data will be lost.')) {
                typeObj.val(typeCommandListId);
                return false;
            }   
        }
        
        typeObj.data('prev_type', typeId);
        
        $('#command_list_form_box').empty();
        subFormBoxObj.empty();
        
        $.ajax({
            url: "/call-command/get-type-form/",
            type: 'POST',
            data: {type_id: typeId},
            dataType: 'json'    
        })
        .done(function(dataResponse) {
            if (dataResponse.status === 1) {
            
                if (typeId === typeCommandListId) {
                    $('#command_list_form_box').html(dataResponse.template);
                } else {
                    $('#type_form_box').html(dataResponse.template);
                }                
            } else if (dataResponse.message.length) {
                createNotify('Error', dataResponse.message, 'error');
            } else {
                createNotify('Error', 'Error, please check logs', 'error');
            }
        })
        .fail(function(error) {
            console.log(error);        
        })
        .always(function() {
                 
        });        
    });
    
    $(document).on('change', '.sub_type_select', function() {
        
        let typeObj = $(this);
        let typeId = parseInt(typeObj.val(), 10);
            
        if (!Number.isInteger(typeId)) {
            return false;
        }        
                
        let elIdentity = typeObj.prop('id');
        let elIndex = elIdentity.replace(/[^0-9]/g, '');         
        let boxId = 'box_' + elIndex;        
        let contentId = 'content_' + elIndex;   
        let boxElement = $('#' + boxId);
        let subFormTemplate = '';
                
        $.ajax({
            url: '/call-command/get-type-form/',
            type: 'POST',
            data: {type_id: typeId, index: elIndex},
            dataType: 'json'    
        })
        .done(function(dataResponse) {
            
            if (dataResponse.status === 1) {
                subFormTemplate = dataResponse.template;  
                let sortObj = $('#commandlist-multipleformdata-' + elIndex + '-sub_sort');
                let sortValue = sortObj.val();
                
                let contentElements = '<div id="' + boxId + '" data-sort="' + sortValue + '" class="sub_forms">' +                     
                        '<div id="' + contentId + '">' +
                            subFormTemplate +     
                        '</div>' + 
                    '</div>' +
                    '<hr />';
                    
                if (!boxElement.length) {
                    $('#type_form_box').append(contentElements);
                } else {                    
                    $('#' + contentId).html(subFormTemplate);
                }    
                                
                setSortByIndex(elIndex, sortValue);                
                                                          
            } else if (dataResponse.message.length) {
                createNotify('Error', dataResponse.message, 'error');
            } else {
                createNotify('Error', 'Error, please check logs', 'error');
            }
        })
        .fail(function(error) {
            console.log(error);        
        })
        .always(function() {                 
        });          
    });
    
    function mappingFormData(form){
        let unIndexedArray = form.serializeArray();
        let result = {};
    
        $.map(unIndexedArray, function(n, i){
            if (n['name'] !== '_csrf-frontend') { 
                result[n['name']] = n['value'];
            }
        });    
        return result;
    } 
        
    function setSortByIndex(elIndex, sortValue) {
        let boxObj = $('#box_' + elIndex);
            
        boxObj.data('sort', sortValue);  
        boxObj.find('#sort').val(sortValue);
        boxObj.find('.head_sort').text(sortValue);
    }               
JS;
$this->registerJs($js);




