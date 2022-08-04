<?php

use modules\taskList\src\forms\UserTaskNoteForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var UserTaskNoteForm $addNoteForm
 */


$form = ActiveForm::begin(['id' => 'add-note-form',
    'action' => Url::to(['/task-list/ajax-add-note', 'userTaskId' => $addNoteForm->userTaskId])]); ?>

<?= $form->field($addNoteForm, 'note')->textarea(['cols' => 6, 'style' => 'resize:none; height:100px']) ?>

<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>
<?php ActiveForm::end(); ?>


<?php
$js = <<<JS
       $(document).on('submit', '#add-note-form', function(event) {
            event.preventDefault();
            
              $.ajax({
                   url: $(this).attr('action'),
                   data: $(this).serialize(),                      
                   type: 'post',                         
                   success: function(response){
                       if(response.userTaskId){
                           $("#modal-sm").modal("hide")
                           $('.js-add_note_task_list[data-usertaskid="'+response.userTaskId+'"]').html(response.truncateNote)
                           $('.js-add_note_task_list[data-usertaskid="'+response.userTaskId+'"]').attr('title', response.note)
                       }
                   }
                });  
        });
JS;

$this->registerJs($js);