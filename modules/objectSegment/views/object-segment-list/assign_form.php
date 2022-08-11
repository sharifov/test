<?php

use kartik\select2\Select2;
use modules\shiftSchedule\src\entities\shift\Shift;
use modules\shiftSchedule\src\forms\UserShiftAssignForm;
use modules\taskList\src\services\TaskListService;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var yii\web\View $this */
/* @var \modules\objectSegment\src\entities\ObjectSegmentList $objectSegment */
/* @var ActiveForm $form */
/* @var \modules\objectSegment\src\forms\ObjectSegmentListAssignForm $model */
?>

    <div class="user-shift-assign_box">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin([
                'id' => 'js_osl_form',
                'options' => ['data-pjax' => 0, 'class' => ''],
                'enableAjaxValidation' => true,
                'enableClientValidation' => false,
                'validationUrl' => Url::to(['/object-segment/object-segment-list/assign-validation'])
            ]) ?>

            <?php echo $form->errorSummary($model) ?>

            <h6>Object segment: <?= $objectSegment->osl_title ?> (<?= $objectSegment->osl_id ?>)</h6>

            <?php echo $form->field($model, 'taskIds', ['options' => []])->widget(Select2::class, [
                'data' => TaskListService::getTaskObjectList($model->objectTypeId),
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select Task', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ]); ?>

            <?= $form->field($model, 'objectSegmentId')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'objectTypeId')->hiddenInput()->label(false) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'id' => 'js_usha_submit']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>


<?php
$multipleAssignUrl = Url::to(['/object-segment/object-segment-list/assign']);
$js = <<<JS
var assignUrl = '$multipleAssignUrl';

$('#js_osl_form').on('beforeSubmit', function (e) {
    e.preventDefault();

    let btnSave = $('#js_usha_submit');
    let btnContent = btnSave.html();
    btnSave.html('<i class="fa fa-cog fa-spin"></i> Processing')
        .addClass('btn-default')
        .prop('disabled', true);

    $.ajax({
       type: 'POST',
       url: assignUrl,
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(dataResponse) {
            if (dataResponse.status === 1) {
                $.pjax.reload({container: '#object-segment-list-pjax', timeout: 10000, async: false});
                
                $('#object_segment_list_assign_modal').modal('hide');
                createNotify('Success', dataResponse.message, 'success');
            } else if (dataResponse.message.length) {
                createNotify('Error', dataResponse.message, 'error');
            } else {
                createNotify('Error', 'Error, please check logs', 'error');
            }
            setTimeout(function () {
                btnSave.html(btnContent).removeClass('btn-default').prop('disabled', false);  
            }, 1000);
       },
       error: function (error) {
            createNotify({title: 'Error', text: 'Internal Server Error. Try again letter.', type: 'error'});
            setTimeout(function () {
                btnSave.html(btnContent).removeClass('btn-default').prop('disabled', false);  
            }, 1000);
       }
    })
    return false;
}); 
JS;
$this->registerJs($js);
