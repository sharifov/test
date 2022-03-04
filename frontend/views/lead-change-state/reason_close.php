<?php

use src\forms\leadflow\TrashReasonForm;
use yii\widgets\ActiveForm;
use yii\bootstrap\Html;

/**
 * @var $reasonForm \src\forms\leadflow\CloseReasonForm
 * @var $reasonStatuses array
 * @var $reasonStatuesCommentRequired array
 */

$jsonReasonStatusCommentRequired = json_encode($reasonStatuesCommentRequired);
$commentRequired = (bool)($reasonStatuesCommentRequired[$reasonForm->reasonKey] ?? false);
?>

<script>pjaxOffFormSubmit('#close-reason-pjax')</script>
<?php
\yii\widgets\Pjax::begin(['enableReplaceState' => false, 'enablePushState' => false, 'id' => 'close-reason-pjax']);

$form = ActiveForm::begin([
    'id' => $reasonForm->formName(),
    'action' => ['lead-change-state/close', 'gid' => $reasonForm->leadGid],
    'options' => [
        'data-pjax' => 1
    ]
]) ?>

<?= $form->errorSummary($reasonForm) ?>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($reasonForm, 'reasonKey')->dropDownList($reasonStatuses, [
                'prompt' => '---',
                'onchange' => "
                var commentRequired = JSON.parse('" . json_encode($reasonStatuesCommentRequired) . "');
                console.log(commentRequired);
                var val = $(this).val();
                if (val in commentRequired && commentRequired[val] == 1) {
                    $('#" . $form->id . "-other-wrapper').addClass('show');
                    $('#" . $form->id . "-duplicate-wrapper').removeClass('show');
                } else {
                    $('#" . $form->id . "-other-wrapper').removeClass('show');
                    $('#" . $form->id . "-duplicate-wrapper').removeClass('show');
                }
            "]) ?>
        </div>
    </div>

    <div class="form-group collapse <?= $commentRequired ? 'show' : '' ?>" id="<?= $form->id ?>-other-wrapper">
        <?= $form->field($reasonForm, 'reason')->textarea(['rows' => 5]) ?>
    </div>

    <div class="actions-btn-wrapper text-center">
        <?= Html::submitButton('<i class="fa fa-close"></i> Close Lead', ['class' => 'btn btn-primary popover-close-btn']) ?>
    </div>

<?php ActiveForm::end();

\yii\widgets\Pjax::end();
