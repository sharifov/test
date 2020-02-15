<?php

/* @var $model modules\qaTask\src\entities\qaTask\QaTask */
/* @var $this yii\web\View */

?>

<?= $this->render('actions/status_history', ['model' => $model]) ?>
<?= $this->render('actions/take', ['model' => $model]) ?>
<?= $this->render('actions/take_over', ['model' => $model]) ?>
<?= $this->render('actions/escalate', ['model' => $model]) ?>
<?= $this->render('actions/close', ['model' => $model]) ?>
<?= $this->render('actions/cancel', ['model' => $model]) ?>
<?= $this->render('actions/return', ['model' => $model]) ?>
<?= $this->render('actions/decide', ['model' => $model]) ?>

<?php
$js = <<<JS

 $(document).on('click', '.btn-modal-show', function(e){        
    e.preventDefault();
    
    let url = $(this).data('url');
    let title = $(this).data('title');
    let modalId = $(this).data('modal-id');
    let modal = $('#' + modalId);
      
    modal.find('.modal-body').html('');
    modal.find('.modal-title').html(title);
    modal.find('.modal-body').load(url, function( response, status, xhr ) {
        //$('#preloader').addClass('d-none');
        if (status == 'error') {
            alert(response);
        } else {
            modal.modal({
              backdrop: 'static',
              show: true
            });
        }
    });
 });

JS;
$this->registerJs($js);
