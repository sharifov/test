<?php

use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var string $modalId */
/** @var string $showUrl */

?>

<?= Html::button('<i class="fa fa-edit"></i> Update all', ['class' => 'btn btn-info update-all-btn']) ?>

<?php
$js = <<<JS
$('body').on('click', '.update-all-btn', function(e) {
    let url = '{$showUrl}';
    e.preventDefault();
    let modalId = '{$modalId}';
    let modal = $('#' + modalId);
    $(modal).find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
    $(modal).find('#' + modalId + '-label').html('Update all');
    $(modal).modal();
    $.post(url, {}, function(data) { $(modal).find('.modal-body').html(data); });
})
JS;

$this->registerJs($js);
