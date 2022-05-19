<?php

/**
 * @var $lead \common\models\Lead
 * @var $errors []
 * @var $totalProfit float
 * @var $mainAgentProfit float
 * @var $mainAgentPercent int
 * @var $splitForm ProfitSplitForm
 */

use common\models\Employee;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ProfitSplit;

/** @var Employee $user */
$user = Yii::$app->user->identity;

if ($user->isAdmin()) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListSplitProfitByRole([Employee::ROLE_AGENT, Employee::ROLE_SUPERVISION]);
}

$js = <<<JS
var mainAgentProfitVal = $mainAgentProfit
var mainAgentPercentVal = $mainAgentPercent

$(function(){
    $(document).on('beforeValidate','#split-form', function (e) {
        $('#new-split-profit-block').remove();
        return true;
    });

    $(document).on('click','#cancel-btn', function (e) {
        e.preventDefault();
        $('#split-profit-modal').modal('hide');
    });
    mainAgentProfitVal = $mainAgentProfit
    mainAgentPercentVal = $mainAgentPercent
});
function mainAgentProfit() {
    $('#main-agent-profit').html(mainAgentProfitVal);
    $('#main-agent-percent').html(mainAgentPercentVal);
    $('.owner-amound').html(mainAgentProfitVal);
    $('.owner-percent').val(mainAgentPercentVal);
}
JS;
$this->registerJs($js);?>
<?php yii\widgets\Pjax::begin(['id' => 'profit' ,'enablePushState' => false]) ?>
<?php if (!empty($errors)) :?>
<div class="alert alert-danger">Some errors happened!
    <?php if (isset($errors["profitsplitform-sumpercent"])) :?>
<br/> <?= $errors["profitsplitform-sumpercent"][0]?>
    <?php endif;?>
    <?php if (isset($errors["profitsplitform-mainagent"])) :?>
<br/> <?= $errors["profitsplitform-mainagent"][0]?>
    <?php endif;?>
</div>
<?php endif;?>
<div id="split-form-notification"></div>
 <?php $form = ActiveForm::begin([
     'options' => ['data-pjax' => true, 'id' => 'split-form'],
     'enableClientValidation' => false,
]); ?>
<div class="row">
    <div class="col-md-4">Total profit: $<?= number_format($totalProfit, 2)?></div>
    <div class="col-md-4">
        <?php if ($lead->employee) : ?>
            Profit for main agent (<b><?= $lead->employee->username?></b>): $<span id="main-agent-profit"><?= number_format($mainAgentProfit, 2)?></span>
            (<span id="main-agent-percent"><?=$mainAgentPercent?></span>%)
        <?php else : ?>
            <i class="fa fa-exclamation-triangle"></i> Main agent not found.
        <?php endif; ?>
    </div>
    <div class="col-md-4">
    <?= Html::button('<i class="fa fa-plus"></i> Add Agent', [
            'id' => 'new-split-profit-button',
            'class' => 'btn btn-success pull-right' ,
        ]); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-4"><label>Agent</label></div>
    <div class="col-md-4"><label>Percentage of split</label></div>
    <div class="col-md-4"><label>Profit</label></div>
</div>
<div id="profit-splits">
    <?php
    if (!empty($splitForm->getProfitSplit())) {
        /** @var ProfitSplit $_split */
        foreach ($splitForm->getProfitSplit() as $key => $_split) {
            echo $this->render('partial/_formSplitProfit', [
                'key' => $_split->isNewRecord
                ? (strpos($key, 'new') !== false ? $key : 'new' . $key)
                : $_split->ps_id,
                'form' => $form,
                'split' => $_split,
                'ownerSplit' => $_split->ps_user_id === $lead->employee_id,
                'userList' => $userList,
                'totalProfit' => $totalProfit,
                'leadId' => $lead->id
            ]);
        }
    }
    ?>
    <div id="new-split-profit-block" style="display: none;">
        <?php $newSplit = new ProfitSplit(); ?>
        <?= $this->render('partial/_formSplitProfit', [
            'key' => '__id__',
            'form' => $form,
            'split' => $newSplit,
            'ownerSplit' => false,
            'userList' => $userList,
            'totalProfit' => $totalProfit,
            'leadId' => $lead->id
        ]) ?>
    </div>
</div>
<?php ob_start(); // output buffer the javascript to register later ?>
<script>
    // add split button
    var split_k = <?php echo isset($key) ? str_replace('new', '', $key) : 0; ?>;
    $('#new-split-profit-button').on('click', function () {
        split_k += 1;
        $('#profit-splits').append($('#new-split-profit-block').html().replace(/__id__/g, 'new' + split_k));
    });

    // remove split button
    $('body').off('click', '.remove-split-button').on('click', '.remove-split-button', function (e) {
        e.preventDefault();
        var amoundDiv = $(this).parent('div').prev();
        var percentDiv = $('input', amoundDiv.prev());
        mainAgentProfitVal = parseFloat(parseFloat(mainAgentProfitVal) + parseFloat(amoundDiv.text().trim())).toFixed(2);
        mainAgentPercentVal = mainAgentPercentVal + parseFloat(percentDiv.val());
        $(this).closest('div.split-row').remove();
        split_k -= 1;
        mainAgentProfit();
    });
</script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>
<div class="btn-wrapper" style="padding-top: 10px;">
    <?=Html::button('<i class="glyphicon glyphicon-remove-circle"></i> Cancel', ['id' => 'cancel-btn','class' => 'btn btn-danger'])?>
    <?=Html::submitButton('<i class="fa fa-save"></i> Confirm', ['id' => 'save-btn','class' => 'btn btn-primary'])?>
</div>
<?php \yii\widgets\ActiveForm::end() ?>
<?php yii\widgets\Pjax::end() ?>
