<?php
echo  frontend\widgets\multipleUpdate\redialAll\UpdateAllShowWidget::widget([
    'validationUrl' => ['/lead-redial/update-all-validation'],
    'action' => ['/lead-redial/update-all'],
    'modalId' => 'modal-df',
    'script' => '$.pjax.reload({container: "#lead-redial-pjax", async: false});',
]);
