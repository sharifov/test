<?php

/** @var array $validationUrl */

use sales\rbac\Auth;

/** @var array $action */
/** @var string $modalId */
/** @var string $ids */
/** @var string $pjaxId */

echo \frontend\widgets\multipleUpdate\lead\MultipleUpdateWidget::widget([
    'validationUrl' => $validationUrl,
    'action' => $action,
    'modalId' => $modalId,
    'ids' => $ids,
    'pjaxId' => $pjaxId,
    'user' => Auth::Identity(),
]);
