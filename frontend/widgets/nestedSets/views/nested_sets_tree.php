<?php
/** @var  $data string */
/** @var  $placeholder string */
/** @var  $currentModelId string */
/** @var  $parentCategoryId string */
/** @var  $attribute string */
/** @var  $name string */
/** @var  $label string */
?>
    <div class='form-group'>
        <?= $label ?>
        <select class="form-control" id='<?= $attribute ?>' name="<?= $name ?>">
        </select>
    </div>
<?php
$js = <<<JS
$(document).ready(function(){

    const nestedSetsSelect = $("#{$attribute}");
    /* add empty option to enable placeholder*/
    nestedSetsSelect.prepend('<option selected=""></option>');
    nestedSetsSelect.select2ToTree({
        treeData: {dataArr: $data}, 
        maximumSelectionLength: 3,
        placeholder:  "$placeholder"   
    });

    const currentModelId = {$currentModelId};
    /* disable option when model has ths same ID */
    if (currentModelId){
      nestedSetsSelect.find(`option[value="${currentModelId}"]`).prop('disabled',true);
    }
    /* set the option "selected" if parent  category ID exists for a model */
    const parentCategoryId = {$parentCategoryId};
    if (parentCategoryId){
      nestedSetsSelect.val(parentCategoryId).trigger("change");
    }   
 
});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);

$css = <<<CSS
#{$attribute} + .select2 .select2-selection{
    background-color: #f4f7fa;
    height: 30px;
    border: 1px solid #e4e9ee;
    box-shadow: none;
    color: #7890a2;
    padding: 3px;
    font-size: 13px;
}
CSS;
$this->registerCss($css);