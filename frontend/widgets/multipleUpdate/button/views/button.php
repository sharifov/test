<?php

use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var string $modalId */
/** @var string $showUrl */
/** @var string $gridId */
/** @var string $buttonText */
/** @var string $buttonClass */
/** @var string $buttonClassAdditional */
/** @var string $headerText */
/** @var string $faIconClass */
?>

<?= Html::button('<i class="fa ' . $faIconClass . '"></i> ' . $buttonText, ['class' => $buttonClassAdditional . ' ' . $buttonClass]) ?>

<?php
$js = <<<JS
$('.{$buttonClass}').on('click', function(e) {
    e.preventDefault();
    
    var ids = $('body').find('#{$gridId}').yiiGridView('getSelectedRows');
    //console.log(ids);
    if (ids.length < 1) {
        createNotifyByObject({title: "{$headerText}", type: "error", text: 'Not selected rows.', hide: true});
        return false;
    }
    ids = ids.join();
     //console.log(ids);
    var url = '{$showUrl}';
    var modalId = '{$modalId}';
    var modal = $('#' + modalId);
    $(modal).find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
    $(modal).find('#' + modalId + '-label').html('{$headerText}');
    $(modal).modal();
    $.post(url, {"ids" : ids}, function(data) { $(modal).find('.modal-body').html(data); })
        .fail(function () { createNotifyByObject({title: "{$headerText}", type: "error", text: 'Server error.', hide: true}); });
})
JS;

$this->registerJs($js);
