<?php
echo  frontend\widgets\multipleUpdate\redialAll\UpdateAllShowWidget::widget([
    'validationUrl' => ['/lead-redial/update-all-validation'],
    'action' => ['/lead-redial/update-all'],
    'modalId' => 'modal-df',
    'script' => "let pjax = $('#lead-redial-pjax'); if (pjax.length) { $.pjax.reload({container: '#lead-redial-pjax', async: false}); }",
]);
