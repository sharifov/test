<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model sales\forms\cases\CasesChangeStatusForm */
/* @var $form yii\widgets\ActiveForm */

$pjaxId = 'pjax-cases-change-status-form';
?>
    <script>
        pjaxOffFormSubmit('#<?=$pjaxId?>');
    </script>
<?php Pjax::begin(['id' => $pjaxId, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="cases-change-status">

    <?php $form = ActiveForm::begin([
        'action' => ['cases/change-status', 'gid' => $model->caseGid],
        'method' => 'post',
        'options' => ['data-pjax' => true]
    ]); ?>

    <?php
    echo $form->errorSummary($model);
    ?>
    <?php
        $reasonCollapse = empty($model->hasErrors('reason')) && empty($model->hasErrors('message'))  ? 'collapse' : '';
        $messageCollapse = empty($model->hasErrors('message')) ? 'collapse' : '';

    ?>
    <?= $form->field($model, 'status')->dropDownList($model->getStatusList(), ['prompt' => '-']) ?>

    <?= $form->field($model, 'reason', ['options' => ['class' => "form-group required {$reasonCollapse}"]])->dropDownList($model->getReasonsList(),['prompt' => '-','id' => 'reason',]) ?>

    <?= $form->field($model, 'message', [
            'options' => [
                    'class' => "form-group required {$messageCollapse}"
            ]
    ])->textarea(['rows' => 3]) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Change Status', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php
$statusReasons = json_encode($statusReasons);
$js = <<<JS
 var statusReasons = $statusReasons,
     reasonField = $('#reason'),
     messageField = $('#caseschangestatusform-message');
 
 $('#caseschangestatusform-status').on('change', function () {
     var val = $(this).val() || null;
     
     reasonField.html('');
        
     if (val in statusReasons) {
          $(reasonField).append('<option value="">-</select>');
         $.each(statusReasons[val], function (i, elem) {
             $(reasonField).append('<option value="'+i+'">' + elem +'</select>');
         });
         reasonField.parent().show();
     } else {
         reasonField.parent().hide();
     }
     messageField.val('').parent().hide();
 });
 
 reasonField.on('change', function () {
     var val = $(this).val() || null;
     
     if (val == 'Other') {
         messageField.parent().show();
     } else {
         messageField.val('').parent().hide();
     }
 });
    
JS;
$this->registerJs($js);
?>
<?php Pjax::end();
