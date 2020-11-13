<?php

use sales\model\call\entity\callCommand\types\CommandList;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var yii\web\View $this */
/* @var yii\widgets\ActiveForm $formType */
/* @var int $typeId */
/* @var CommandList $model */
?>
    <?php
        $formName = $model->formName();
        $multipleInputId = 'multiple_form_command_list';
    ?>

    <h5>Command List</h5>

    <?php $formType = ActiveForm::begin([
            'id' => $formName,
            'class' => 'command_type_form command_list_form',
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'validateOnChange' => true,
            'validateOnBlur' => true,
            'validationUrl' => Url::to(['call-command/validate-command-list-form', 'type_id' => $typeId]),
        ]); ?>

        <?php echo $formType->field($model, 'multipleFormData')->widget(MultipleInput::class, [
            'id' => $multipleInputId,
            'min' => 1,
            'max' => 20,
            'sortable' => true,
            'enableError' => true,
            'allowEmptyList' => false,
            'showGeneralError' => true,
            'addButtonOptions' => [
                'class' => 'multiple-input-list__btn js-input-plus btn btn-default btn_add_line',
                'label' => '<i class="glyphicon glyphicon-plus"></i>',
                'options' => [
                    'disable' => '1',
                ],
            ],
            'columns' => [
                [
                    'title' => 'Sort',
                    'name' => 'sub_sort',
                    'options' => [
                        'class' => 'form-control input_command_line sort_command_line',
                        'readonly' => true,
                        'style' => 'width: 35px;',
                    ],
                    'headerOptions' => [
                        'style' => 'width: 25px;',
                    ],
                ],
                [
                    'title' => 'Type Id',
                    'name' => 'sub_type',
                    'type'  => 'dropDownList',
                    'items' => $model::ALLOWED_TYPE_LIST,
                    'headerOptions' => [
                        'style' => 'width: 260px;',
                    ],
                    'options' => [
                        'prompt' => '---',
                        'class' => 'form-control input_command_line sub_type_select',
                    ],
                ],
                [
                    'name' => 'model_id',
                    'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,
                ],
            ]
        ])->label(false) ?>

    <?php $formType::end() ?>

<?php
    $js = <<<JS
    var formCommandListObj = $('#$formName');
    var multipleInputObj = $('#$multipleInputId');
    
    formCommandListObj.on('ajaxBeforeSend', function(event, jqXHR, settings) {
        $('#$formName .form-control').removeClass('has-error').prop('title', '');
    });

    formCommandListObj.on('ajaxComplete', function(event, jqXHR, textStatus) {
        $.each(jqXHR.responseJSON, function(keyEl, msgs) {

            var splitKeyEl = keyEl.split('-');
            splitKeyEl[0] = splitKeyEl[0] + '-multipleformdata';
            var elementId = splitKeyEl.join('-');

            if (msgs.length) {
                var message = msgs.join(',');
                $('#' + elementId).addClass('has-error').prop('title', message);
            }
        });
    });
    
    $('.btn_add_line').on('click', function(e) {        
        formCommandListObj.find('.input_command_line').each(function (index, el) {
            let value = $(el).val();
            if (!value.length) {
                $(el).addClass('has-error').prop('title', 'Field is required');
            }
        });
            
        if (formCommandListObj.find('.has-error').length) {
            return false;
        }
    });
    
    multipleInputObj.on('afterInit', function() {
        $('.sort_command_line').first().val('0');        
        multipleInputObj.find('.list-cell__drag').first()
            .html('<i class="fa fa-hand-paper-o hand" title="Please drug for change sorting"></i>');
    });
    
    multipleInputObj.on('afterDeleteRow', function(e, row, currentIndex) {        
        let childSubTypeId = row.find('.sub_type_select').prop('id');
        let elIndex = getIndexById(childSubTypeId);
        
        $('#box_' + elIndex).remove();
        reSorting();      
    });
    
    multipleInputObj.on('afterAddRow', function(e, row, currentIndex) {
    
        let sortCommandLines = $('.sort_command_line');    
        let preLastSortEl = sortCommandLines.get(sortCommandLines.length - 2);
        let preLastSortId = preLastSortEl.getAttribute('id');
        let sortValue = 0;
                 
        if (preLastSortId.length > 0) {             
            sortValue = $('#' + preLastSortId).val();           
        } 
        sortValue++;
        
        let lastSortObj = sortCommandLines.last();        
        lastSortObj.val(sortValue);        
    });
    
    multipleInputObj.on('afterDropRow', function(e, item) {
        reSorting(); 
    });
    
    function reSorting() {
        let sortValue = 0;
        $('#multiple_form_command_list').find('.sort_command_line').each(function(index, el) {
            $(el).val(sortValue);
            
            let elId = $(el).prop('id');
            let elIndex = getIndexById(elId);            
            setSortByIndex(elIndex, sortValue);
                                               
            sortValue++;
        });
        
        let items = $('#type_form_box .sub_forms'); 
        let arItems = $.makeArray(items);
        arItems.sort(function(a, b) {
            return $(a).data('sort') - $(b).data('sort')
        });
        $('#type_form_box').empty();
        $(arItems).appendTo('#type_form_box');
    }
    
    function getIndexById(elId) {
        return elId.replace(/[^0-9]/g, '')
    }
    
    function setSortByIndex(elIndex, sortValue) {
        let boxObj = $('#box_' + elIndex);
            
        boxObj.data('sort', sortValue);  
        boxObj.find('#sort').val(sortValue);
        boxObj.find('.head_sort').text(sortValue);
    }
    
JS;
$this->registerJs($js);

$css = <<<CSS
    #CommandList .table th, 
    #CommandList .table td { 
        padding: 0.3rem;    
    }
    .table.multiple-input-list tr > th {
        border: 0!important;
    }  
    .hand {
        margin-left: 8px;
        display: block;        
    }
CSS;
$this->registerCss($css);
?>
