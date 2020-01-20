<?php

use common\models\Employee;

/** @var array $validationUrl */
/** @var array $action */
/** @var string $modalId */
/** @var string $ids */
/** @var string $pjaxId */
/** @var Employee $user */

echo \frontend\widgets\multipleUpdate\lead\MultipleUpdateWidget::widget([
    'validationUrl' => $validationUrl,
    'action' => $action,
    'modalId' => $modalId,
    'ids' => $ids,
    'pjaxId' => $pjaxId,
    'user' => $user,
]);
