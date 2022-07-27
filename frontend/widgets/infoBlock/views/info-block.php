<?php
/**@var \common\models\InfoBlock $model */

$description = $title = 'Not Found InfoBlock with key (' . $key . ') ';

if (!empty($model)) {
    $description = $model->ib_description;
    $title = $model->ib_title;
}

?>
    <div class="js-info_block_description" style="display: none;">
        <?= $description ?>
    </div>
<?php
$js = <<<JS

 $(document).on('click', '{$btnEl}', function (e) { 
        e.preventDefault();
    
        let infoData = $('.js-info_block_description').html();
        let modal = $('#modal-df');
        modal.find('.modal-body').html(infoData);
        modal.modal('show');     
        modal.find('.modal-title').html('<i class="fa fa-info-circle"></i> '+'{$title}');
    });

JS;
$this->registerJs($js, \yii\web\View::POS_READY);
