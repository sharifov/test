<?php
/**
 * @var $lead \common\models\Lead
 * @var $errors []
 * @var $totalTips float
 * @var $mainAgentTips float
 * @var $splitForm TipsSplitForm
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TipsSplit;

if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByRole('agent');
}

$js = <<<JS
$(function(){
    $(document).on('beforeValidate','#split-form', function (e) {
        $('#new-split-block').remove();
        return true;
    });

    $(document).on('click','#cancel-btn', function (e) {
        e.preventDefault();
        $('#split-tips-modal').modal('hide');
    });
    mainAgentTips();
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
<?php if(!empty($errors)):?>
<div class="alert alert-danger">Some errors happened!
<?php if(isset($errors["tipssplitform-sumpercent"])):?>
<br/> <?= $errors["tipssplitform-sumpercent"][0]?>
<?php endif;?>
<?php if(isset($errors["tipssplitform-mainagent"])):?>
<br/> <?= $errors["tipssplitform-mainagent"][0]?>
<?php endif;?>
</div>
<?php endif;?>
 <?php $form = ActiveForm::begin([
     'options' => ['data-pjax' => true, 'id' => 'split-form'],
     'enableClientValidation' => false,
]); ?>
<div class="row">
	<div class="col-md-4">Total tips: $<?= number_format($totalTips,2)?></div>
	<div class="col-md-4">Tips for main agent (<b><?= $lead->employee->username?></b>): $<span id="main-agent-tips"><?= $mainAgentTips?></span></div>
	<div class="col-md-4">
	<?= Html::button('<span class="btn-icon"><i class="fa fa-plus"></i></span><span>Add Agent</span>', [
            'id' => 'new-split-button',
            'class' => 'btn btn-success btn-with-icon pull-right' ,
        ]); ?>
	</div>
</div>
<div class="row">
	<div class="col-md-4"><label>Agent</label></div>
	<div class="col-md-4"><label>Percentage of split</label></div>
	<div class="col-md-4"><label>Tips</label></div>
</div>
<div id="tips-splits">
    <?php
    if(!empty($splitForm->getTipsSplit())){
        foreach ($splitForm->getTipsSplit() as $key => $_split) {
            echo $this->render('partial/_formSplitTips', [
                'key' => $_split->isNewRecord
                ? (strpos($key, 'new') !== false ? $key : 'new' . $key)
                : $_split->ts_id,
                'form' => $form,
                'split' => $_split,
                'userList' => $userList,
                'totalTips' => $totalTips,
            ]);
        }
    }
    ?>
    <div id="new-split-block" style="display: none;">
        <?php $newSplit = new TipsSplit(); ?>
        <?= $this->render('partial/_formSplitTips', [
            'key' => '__id__',
            'form' => $form,
            'split' => $newSplit,
            'userList' => $userList,
            'totalTips' => $totalTips,
        ]) ?>
    </div>
</div>
<?php ob_start(); // output buffer the javascript to register later ?>
<script>
    // add split button
    var split_k = <?php echo isset($key) ? str_replace('new', '', $key) : 0; ?>;
    $('#new-split-button').on('click', function () {
        split_k += 1;
        $('#tips-splits').append($('#new-split-block').html().replace(/__id__/g, 'new' + split_k));
    });

    // remove split button
    $(document).on('click', '.remove-split-button', function () {
    	$(this).closest('div.split-row').remove();
    	split_k -= 1;
    	mainAgentTips();
    });
</script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>
<div class="btn-wrapper">
    <?=Html::button('<span class="btn-icon"><i class="glyphicon glyphicon-remove-circle"></i></span><span>Cancel</span>', ['id' => 'cancel-btn','class' => 'btn btn-danger btn-with-icon'])?>
    <?=Html::submitButton('<span class="btn-icon"><i class="fa fa-save"></i></span><span>Confirm</span>', ['id' => 'save-btn','class' => 'btn btn-primary btn-with-icon'])?>
</div>
<?php \yii\widgets\ActiveForm::end() ?>
<?php yii\widgets\Pjax::end() ?>