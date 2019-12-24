<?php

use frontend\widgets\lead\editTool\Url;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var string $modalId */
/** @var Url $url */

?>

<?= Html::button('<i class="fa fa-edit"></i> Edit', ['class' => 'btn btn-info edit-lead-btn']) ?>

<?php
$js = <<<JS
$('body').on('click', '.edit-lead-btn', function(e) {
    e.preventDefault();
    var url = '{$url->url}';
    var modalId = '{$modalId}';
    var modal = $('#' + modalId);
    $(modal).find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
    $(modal).find('#' + modalId + '-label').html('Edit');
    $(modal).modal();
    $.post(url, {$url->getData()}, function(data) { $(modal).find('.modal-body').html(data.data); });
})
JS;
$this->registerJs($js);
