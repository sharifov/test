<?php

use sales\model\call\entity\callCommand\CallCommand;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\CallCommand */
/* @var string $typeForm */


$typeName = CallCommand::getTypeName($model->ccom_type_id);

$name = $model->ccom_name ? ', Name: ' . $model->ccom_name : '';

$this->title = 'Update Call Command. Type: ' . $typeName . ', Id: ' . $model->ccom_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Commands', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccom_id, 'url' => ['view', 'id' => $model->ccom_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="call-command-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>


<?php

$typeCommandList = CallCommand::TYPE_COMMAND_LIST;

$js = <<<JS

    var typeCommandListId = $typeCommandList;
    var parentId = $model->ccom_id;

    $(document).ready(function() {
        
        let typeObj = $('#callCommandTypeId');
        let typeId = parseInt(typeObj.val(), 10);
        
        if (!Number.isInteger(typeId)) {
            return false;
        }
        
        $.ajax({
            url: '/call-command/get-type-form/?model_id=' + parentId,
            type: 'POST',
            data: {type_id: typeId},
            dataType: 'json'    
        })
        .done(function(dataResponse) {
            if (dataResponse.status === 1) {                
                if (typeId === typeCommandListId) {
                    $('#command_list_form_box').html(dataResponse.template);
                    getSubForms(parentId);                    
                } else {
                    $('#type_form_box').html(dataResponse.template);
                }                               
            } else {
                createNotify('Error', dataResponse.message, 'error');
            } 
        })
        .fail(function(error) {
            console.log(error);        
        })
        .always(function() {                 
        });        
    }); 
    
    function getSubForms(parentId) 
    {        
        $.ajax({
            url: '/call-command/get-list-sub-forms/',
            type: 'GET',
            data: {parent_id: parentId},
            dataType: 'json'    
        })
        .done(function(dataResponse) {
            if (dataResponse.status === 1) {                
                $('#type_form_box').html(dataResponse.template);                         
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
    }
           
JS;

$this->registerJs($js);

