<?php

use modules\taskList\abac\TaskListAbacObject;
use modules\taskList\src\forms\UserTaskNoteForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var UserTaskNoteForm $addNoteForm
 * @var TaskListAbacObject $abacDto
 */

$urlToDeleteNote = Url::to(['/task-list/ajax-delete-note', 'userTaskId' => $addNoteForm->userTaskId], true);

$form = ActiveForm::begin([
    'id' => 'add-note-form',
    'options' => [
        'class' => 'lead-user-tasks-note',
    ],
]); ?>

<?= $form->field($addNoteForm, 'note')->textarea([
    'cols' => 6,
    'style' => 'resize:none; height:100px',
    'class' => 'lead-user-tasks-note__textarea form-control rounded',
    'placeholder' => 'Type your notes',
]); ?>

<div class="form-group lead-user-tasks-note__btns">
    <?php if (!empty($addNoteForm->note)) {
        echo Html::a('<i class="fa fa-trash" aria-hidden="true"></i> Delete', $urlToDeleteNote, [
            'class' => 'btn btn-danger lead-user-tasks-note__delete',
        ]);
    }

    /** @abac TaskListAbacObject::OBJ_USER_TASK, TaskListAbacObject::ACTION_ADD_NOTE, Access to delete UserTask Note */
    if (Yii::$app->abac->can($abacDto, TaskListAbacObject::OBJ_USER_TASK, TaskListAbacObject::ACTION_REMOVE_NOTE)) {
        echo Html::submitButton('Save', [
            'class' => 'btn btn-success lead-user-tasks-note__submit',
            'data' => [
                'type' => 'add',
            ],
        ]);
    } ?>
</div>
<?php ActiveForm::end(); ?>


<?php
$js = <<<JS
    function enableDisableSubmitBtn() {
        let textareaEl = $(document).find('.lead-user-tasks-note__textarea')['0'];
        let submBtnEl = $(document).find('.lead-user-tasks-note__submit')['0'];

        if (submBtnEl && textareaEl) {
            if (textareaEl.value.length) {
                $(submBtnEl).addClass('btn-success').removeClass('btn-secondary').attr('disabled', false);
            } else {
                $(submBtnEl).addClass('btn-secondary').removeClass('btn-success').attr('disabled', true);
            }
        }
    }
    
    function sendNoteRequest(url, successCallback, data = null) {
        return $.ajax({
            url: url,
            data: data,                      
            type: 'post',                         
            success: function(response) {
                successCallback(response);
            }
        }); 
    }
    
    enableDisableSubmitBtn();
    $(document).on('input', '.lead-user-tasks-note__textarea', function(event) {
        enableDisableSubmitBtn();
    });
    
    $('.lead-user-tasks-note__delete').on('click', function (event) {
        event.preventDefault();
        let href = $(this).attr('href');

        sendNoteRequest(href, (response) => {
            if (response.isSuccess) {
                console.log('delete');
                let taskEl = $('.js-add_note_task_list[data-usertaskid="'+response.userTaskId+'"]');
                taskEl.html('Add note').removeClass('active').attr('data-original-title', '');
                $("#modal-sm").modal("hide");
            }
        });
    });
    
    $(document).on('submit', '#add-note-form', function(event) {
        event.preventDefault();
        let href = $(this).attr('action');
        let data = $(this).serialize();
        
        sendNoteRequest(href, (response) => {
            if(response.userTaskId) {
                console.log('submit');
                let taskEl = $('.js-add_note_task_list[data-usertaskid="'+response.userTaskId+'"]');
                taskEl.html('View note').addClass('active').attr('data-original-title', response.note);
                $("#modal-sm").modal("hide");
            }
        }, data);
    });
JS;

$this->registerJs($js);