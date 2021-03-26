<?php

use common\models\Employee;
use modules\order\src\widgets\multipleUpdate\order\MultipleUpdateWidget;

/** @var array $validationUrl */
/** @var array $action */
/** @var string $modalId */
/** @var string $ids */
/** @var string $pjaxId */
/** @var Employee $user */

echo MultipleUpdateWidget::widget([
    'validationUrl' => $validationUrl,
    'action' => $action,
    'modalId' => $modalId,
    'ids' => $ids,
    'pjaxId' => $pjaxId,
    'user' => $user,
    'script' => "$('.multiple-update-summary').slideDown();",
]);
