<?php

/**
 * @var $lead \common\models\Lead
 * @var $errors []
 * @var $totalTips float
 * @var $mainAgentTips float
 * @var $splitForm TipsSplitForm
 */

use common\models\Employee;
use modules\lead\src\abac\LeadAbacObject;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TipsSplit;

/** @var Employee $user */
$user = Yii::$app->user->identity;

if ($user->isAdmin()) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByRole(Employee::ROLE_AGENT);
}
/** @abac null, LeadAbacObject::CHANGE_SPLIT_TIPS, LeadAbacObject::ACTION_UPDATE, hide split tips edition */
$changeSplitTips = Yii::$app->abac->can(null, LeadAbacObject::CHANGE_SPLIT_TIPS, LeadAbacObject::ACTION_UPDATE);

$js = <<<JS
$(function(){
    $(document).on('beforeValidate','#split-form', function (e) {
        $('#new-split-tips-block').remove();
        return true;
    });

    $(document).on('click','#cancel-btn', function (e) {
        e.preventDefault();
        $('#split-tips-modal').modal('hide');
    });
    // mainAgentTips();
});
function mainAgentTips(){
    var total = $totalTips;
    var agentTips = 0;
    var sum = 0;
    $.each($('.tips-amount'), function( key, obj ) {
      sum += parseFloat($(obj).val());
    });
    $('#main-agent-tips').html(total-sum);
}
JS;
$this->registerJs($js);?>
<?php yii\widgets\Pjax::begin(['id' => 'tips' ,'enablePushState' => false]) ?>
<?php if (!empty($errors)) :?>
<div class="alert alert-danger">Some errors happened!
    <?php if (isset($errors["tipssplitform-sumpercent"])) :?>
<br/> <?= $errors["tipssplitform-sumpercent"][0]?>
    <?php endif;?>
    <?php if (isset($errors["tipssplitform-mainagent"])) :?>
<br/> <?= $errors["tipssplitform-mainagent"][0]?>
    <?php endif;?>
</div>
<?php endif;?>
 <?php $form = ActiveForm::begin([
     'options' => ['data-pjax' => true, 'id' => 'split-form'],
     'enableClientValidation' => false,
]); ?>
<div class="d-flex justify-content-between align-items-center">
    <div><h6><b>Total tips: $<?= number_format($totalTips, 2)?></b></h6></div>
    <div>
        <?php if ($changeSplitTips) :?>
            <?= Html::button('<i class="fa fa-plus"></i> Add Agent', [
                 'id' => 'new-split-tips-button',
                 'class' => 'btn btn-success pull-right' ,
             ]); ?>
        <?php endif;?>
    </div>
</div>
<div class="row">
    <div class="col-md-4"><label>Agent</label></div>
    <div class="col-md-4"><label>Percentage of split</label></div>
    <div class="col-md-4"><label>Tips</label></div>
</div>
<div id="tips-splits">
    <?php
    if (!empty($splitForm->getTipsSplit())) {
        foreach ($splitForm->getTipsSplit() as $key => $_split) {
            echo $this->render('partial/_formSplitTips', [
                'key' => $_split->isNewRecord
                ? (strpos($key, 'new') !== false ? $key : 'new' . $key)
                : $_split->ts_id,
                'form' => $form,
                'split' => $_split,
                'userList' => $userList,
                'totalTips' => $totalTips,
                'changeSplitTips' => $changeSplitTips
            ]);
        }
    }
    ?>
    <?php if ($changeSplitTips) :?>
         <div id="new-split-tips-block" style="display: none;">
             <?php $newSplit = new TipsSplit(); ?>
             <?= $this->render('partial/_formSplitTips', [
                 'key' => '__id__',
                 'form' => $form,
                 'split' => $newSplit,
                 'userList' => $userList,
                 'totalTips' => $totalTips,
                 'changeSplitTips' => $changeSplitTips
             ]) ?>
         </div>
    <?php endif;?>
</div>
<?php ob_start(); // output buffer the javascript to register later ?>
<script>
    // add split button
    var split_k = <?php echo isset($key) ? str_replace('new', '', $key) : 0; ?>;
    $('#new-split-tips-button').on('click', function () {
        split_k += 1;
        $('#tips-splits').append($('#new-split-tips-block').html().replace(/__id__/g, 'new' + split_k));
    });

    // remove split button
    $(document).on('click', '.remove-split-button', function () {
        $(this).closest('div.split-row').remove();
        split_k -= 1;
        mainAgentTips();
    });
</script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>
<div class="btn-wrapper text-center">
    <?=Html::button('<i class="glyphicon glyphicon-remove-circle"></i> Cancel', ['id' => 'cancel-btn','class' => 'btn btn-danger'])?>

    <?php if ($changeSplitTips) :?>
        <?= Html::submitButton('<i class="fa fa-save"></i> Confirm', ['id' => 'save-btn','class' => 'btn btn-primary'])?>
    <?php endif;?>
</div>
<?php \yii\widgets\ActiveForm::end() ?>
<?php yii\widgets\Pjax::end() ?>