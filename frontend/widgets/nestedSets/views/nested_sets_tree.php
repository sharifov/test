<?php
/** @var  $data string */

/** @var  $placeholder string */
/** @var  $currentModelId string */
/** @var  $parentCategoryId string */
/** @var  $attribute string */
/** @var  $name string */
/** @var  $label string */
/** @var  $disabled bool */
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
    if ("$disabled"){
        nestedSetsSelect.prop("disabled", true);
    }
    /* add empty option to enable placeholder*/
    nestedSetsSelect.prepend('<option selected=""></option>');
    nestedSetsSelect.select2ToTree({
        treeData: {dataArr: $data}, 
        maximumSelectionLength: 3,
        placeholder:  "$placeholder",
        allowClear: true,
        templateResult: formatState,
         templateSelection: formatState   
    });
    function formatState (state) {
			if (!state.disabled) {
              return state.text;
			}
            return $('<span><i class="fa fa-lock"></i> ' + state.text + '</span>');
		}
   
    /* set the option "selected" if parent  category ID exists for a model */
    const parentCategoryId = "$parentCategoryId";
    if (parentCategoryId){
      nestedSetsSelect.val(parentCategoryId).trigger('change.select2');
    }   
 
});
JS;
$this->registerJs($js, \yii\web\View::POS_END);

$css = <<<CSS
#{$attribute} + .select2 .select2-selection{
    background-color: #f4f7fa;
    height: 30px;
    border: 1px solid #e4e9ee;
    box-shadow: none;
    color: #7890a2;
    padding: 3px;
    font-size: 13px;
    min-height: 30px;
}
#{$attribute} + .select2 .select2-selection span.select2-selection__rendered{
    padding-top: 0;
}
.select2-container--default .select2-selection--single .select2-selection__placeholder{ 
    color: #7890a2; 
 }  
CSS;
$this->registerCss($css);