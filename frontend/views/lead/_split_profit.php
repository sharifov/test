<?php
/**
 * @var $lead \common\models\Lead
 * @var $errors []
 * @var $totalProfit float
 * @var $mainAgentProfit float
 * @var $splitForm ProfitSplitForm
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ProfitSplit;

if (Yii::$app->user->identity->canRole('admin')) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByRole('agent');
}

$js = <<<JS
$(function(){
    $(document).on('beforeValidate','#split-form', function (e) {
        $('#new-split-profit-block').remove();
        return true;
    });

    $(document).on('click','#cancel-btn', function (e) {
        e.preventDefault();
        $('#split-profit-modal').modal('hide');
    });
    mainAgentProfit();
});
function mainAgentProfit(){
    var total = $totalProfit;
    var agentProfit = 0;
    var sum = 0;
    $.each($('.profit-amount'), function( key, obj ) {
      sum += parseFloat($(obj).html());
    });
    $('#main-agent-profit').html(total-sum);
}
JS;
$this->registerJs($js);?>
<?php yii\widgets\Pjax::begin(['id' => 'profit' ,'enablePushState' => false]) ?>
<?php if(!empty($errors)):?>
<div class="alert alert-danger">Some errors happened!
<?php if(isset($errors["profitsplitform-sumpercent"])):?>
<br/> <?= $errors["profitsplitform-sumpercent"][0]?>
<?php endif;?>
<?php if(isset($errors["profitsplitform-mainagent"])):?>
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
	<div class="col-md-4">Total profit: $<?= number_format($totalProfit,2)?></div>
	<div class="col-md-4">Profit for main agent (<b><?= $lead->employee->username?></b>): $<span id="main-agent-profit"><?= $mainAgentProfit?></span></div>
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
    if(!empty($splitForm->getProfitSplit())){
        foreach ($splitForm->getProfitSplit() as $key => $_split) {
            echo $this->render('partial/_formSplitProfit', [
                'key' => $_split->isNewRecord
                ? (strpos($key, 'new') !== false ? $key : 'new' . $key)
                : $_split->ps_id,
                'form' => $form,
                'split' => $_split,
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
    $(document).on('click', '.remove-split-button', function () {
    	$(this).closest('div.split-row').remove();
    	split_k -= 1;
    	mainAgentProfit();
    });
</script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>
<div class="btn-wrapper">
    <?=Html::button('<i class="glyphicon glyphicon-remove-circle"></i> Cancel', ['id' => 'cancel-btn','class' => 'btn btn-danger'])?>
    <?=Html::submitButton('<i class="fa fa-save"></i> Confirm', ['id' => 'save-btn','class' => 'btn btn-primary'])?>
</div>
<?php \yii\widgets\ActiveForm::end() ?>
<?php yii\widgets\Pjax::end() ?>