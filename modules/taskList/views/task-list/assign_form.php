<?php

use kartik\select2\Select2;
use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\shiftSchedule\src\entities\shift\Shift;
use modules\shiftSchedule\src\forms\UserShiftAssignForm;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\forms\TaskListAssignForm;
use modules\taskList\src\services\TaskListService;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var yii\web\View $this */
/* @var TaskList $taskList */
/* @var ActiveForm $form */
/* @var TaskListAssignForm $model */
?>

    <div class="user-shift-assign_box">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin([
                'id' => 'js_tl_form',
                'options' => ['data-pjax' => 0, 'class' => ''],
                'enableAjaxValidation' => true,
                'enableClientValidation' => false,
                'validationUrl' => Url::to(['/task/task-list/assign-validation'])
            ]) ?>

            <?php echo $form->errorSummary($model) ?>

            <h6>Task list: <?= $taskList->tl_title ?> (<?= $taskList->tl_id ?>)</h6>

            <?php echo $form->field($model, 'objectSegmentIds', ['options' => []])->widget(Select2::class, [
                'data' => ObjectSegmentList::getList($model->objectTypeId),
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select Object Segment', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ]); ?>

            <?= $form->field($model, 'taskListId')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'objectTypeId')->hiddenInput()->label(false) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'id' => 'js_usha_submit']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>


<?php
$multipleAssignUrl = Url::to(['/task/task-list/assign']);
$js = <<<JS
var assignUrl = '$multipleAssignUrl';

$('#js_tl_form').on('beforeSubmit', function (e) {
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
                $.pjax.reload({container: '#task-list-pjax', timeout: 10000, async: false});
                
                $('#task_list_assign_modal').modal('hide');
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
