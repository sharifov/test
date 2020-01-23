<?php

/** @var array $validationUrl */
/** @var array $action */
/** @var string $modalId */
/** @var string $ids */
/** @var string $pjaxId */

echo \frontend\widgets\multipleUpdate\cases\MultipleUpdateWidget::widget([
    'validationUrl' => $validationUrl,
    'action' => $action,
    'modalId' => $modalId,
    'ids' => $ids,
    'pjaxId' => $pjaxId,
    'summaryIdentifier' => '.multiple-update-summary .card-body',
    'script' => "$('.multiple-update-summary').slideDown();",
]);
