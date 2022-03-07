<?php

use src\forms\leadflow\TrashReasonForm;
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

<script>pjaxOffFormSubmit('#close-reason-pjax')</script>
<?php
Pjax::begin(['enableReplaceState' => false, 'enablePushState' => false, 'id' => 'close-reason-pjax']);

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
                  var val = $(this).val();
                  $.ajax({
                      type: 'get',
                      url: '$url' + '&reasonKey=' + val,
                      cache: false,
                      dataType: 'json',
                      beforeSend: function () {
                          $('#close-reason-submit').addClass('disabled').prop('disabled', true);           
                      },
                      success: function (data) {
                          $('#lead-close-reason-description').hide();
                          if (data.description) {
                              $('#lead-close-reason-description').html(data.description);
                              $('#lead-close-reason-description').show();
                          }
                          
                          if (data.commentRequired) {
                              $('#" . $form->id . "-other-wrapper').show();
                          } else {
                              $('#" . $form->id . "-other-wrapper').hide();
                          }
                      },
                      complete: function () {
                          $('#close-reason-submit').removeClass('disabled').prop('disabled', false);                                     
                      },
                      error: function (xhr) {
                          createNotify('Error', xhr.responseText, 'error');
                      }
                  });
            "]) ?>

            <div class="alert alert-info" style="display: none;" id="lead-close-reason-description"></div>
        </div>
    </div>

    <div class="form-group collapse <?= $commentRequired ? 'show' : '' ?>" id="<?= $form->id ?>-other-wrapper">
        <?= $form->field($reasonForm, 'reason')->textarea(['rows' => 3]) ?>
    </div>

    <div class="actions-btn-wrapper text-center">
        <?= Html::submitButton('<i class="fa fa-close"></i> Close Lead', ['class' => 'btn btn-primary popover-close-btn', 'id' => 'close-reason-submit']) ?>
    </div>

<?php ActiveForm::end();

Pjax::end();
