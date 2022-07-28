<?php

use kartik\select2\Select2;
use modules\featureFlag\FFlag;
use src\forms\leadflow\TrashReasonForm;
use src\model\leadStatusReason\entity\LeadStatusReason;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\bootstrap\Html;
use yii\widgets\Pjax;

/**
 * @var $reasonForm \src\forms\leadflow\CloseReasonForm
 * @var $reasonStatuses array
 * @var $reasonStatuesCommentRequired array
 */

$commentRequired = (bool)($reasonStatuesCommentRequired[$reasonForm->reasonKey] ?? false);

$url = Url::to(['/lead-change-state/ajax-changed-close-reason', 'gid' => $reasonForm->leadGid]);
?>

<script>
    pjaxOffFormSubmit('#close-reason-pjax')
    function getReasonList(ajaxUrl, formId, reasonKey) {
        $.ajax({
            type: 'get',
            url: ajaxUrl + '&reasonKey=' + reasonKey,
            cache: false,
            dataType: 'json',
            beforeSend: function () {
                $('#close-reason-submit').addClass('disabled').prop('disabled', true);
            },
            success: function (data) {
                $('#lead-close-reason-description-div').hide();
                if (data.description) {
                    $('#lead-close-reason-description-text').html(data.description);
                    $('#lead-close-reason-description-div').show();
                }

                    let objComment = $('#' + formId + '-other-wrapper-comment');
                    let objOriginId = $('#' + formId + '-other-wrapper-originId');
                    if (data.commentRequired) {
                        objComment.show();
                    } else {
                        objComment.hide();
                    }

                    if (data.originRequired) {
                        objOriginId.show();
                    } else {
                        objOriginId.hide();
                    }
                },
                complete: function () {
                    $('#close-reason-submit').removeClass('disabled').prop('disabled', false);
                },
                error: function (xhr) {
                    createNotify('Error', xhr.responseText, 'error');
                }
            });
        }
    </script>

<?php
Pjax::begin(['enableReplaceState' => false, 'enablePushState' => false, 'id' => 'close-reason-pjax']);

$form = ActiveForm::begin([
    'id' => $reasonForm->formName(),
    'action' => ['lead-change-state/close', 'gid' => $reasonForm->leadGid],
    'options' => [
        'data-pjax' => 1
    ]
]) ?>
<script>
    var ajaxUrl = '<?=$url?>';
    var formId = '<?=$form->id?>';
</script>
<?= $form->errorSummary($reasonForm) ?>

    <div class="row">
        <div class="col-sm-12">


            <?= $form->field($reasonForm, 'reasonKey')->widget(Select2::class, [
                'options' => [
                    'placeholder' => '--',
                    'multiple' => false,
                    'id' => 'lead-reason-key'
                ],
                'pluginEvents' => [
                   "change" => 'function(event) {getReasonList(ajaxUrl, formId, event.target.value)}'
                ],
                'data' => $reasonStatuses,
                'size' => Select2::SIZE_SMALL
            ]) ?>

            <?php /*= $form->field($reasonForm, 'reasonKey')->dropDownList($reasonStatuses, [
                'prompt' => '---',
                'onchange' => $jsOnChange])*/ ?>

            <div class="alert alert-secondary" style="display: none;" id="lead-close-reason-description-div">
                <h5 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> Notification</h5>
                <p id="lead-close-reason-description-text"></p>
            </div>
        </div>
    </div>

    <div class="form-group collapse <?= $commentRequired ? 'show' : '' ?>" id="<?= $form->id ?>-other-wrapper-comment">
        <?= $form->field($reasonForm, 'reason')->textarea(['rows' => 3]) ?>
    </div>
<?php if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_VALIDATE_CLOSING_REASON_DUPLICATED)) : ?>
    <div class="form-group collapse <?= $reasonForm->reasonKey == LeadStatusReason::REASON_KEY_DUPLICATED ? 'show' : '' ?>"
         id="<?= $form->id ?>-other-wrapper-originId">
        <?= $form->field($reasonForm, 'originLeadId')->textInput() ?>
    </div>
<?php endif; ?>

    <div class="actions-btn-wrapper text-center">
        <?= Html::submitButton('<i class="fa fa-close"></i> Close Lead', ['class' => 'btn btn-primary popover-close-btn', 'id' => 'close-reason-submit']) ?>
    </div>

<?php ActiveForm::end();

Pjax::end();
