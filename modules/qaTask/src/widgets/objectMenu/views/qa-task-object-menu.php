<?php

use yii\bootstrap4\Html;

/** @var string $viewUrl */
/** @var string $addUrl */
/** @var int $active */
/** @var int $total */

?>
<div class="dropdown" style="margin-left: 7px">
    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">QA Tasks (<?= $active ?> / <?= $total ?>)</button>
    <div class="dropdown-menu">
        <?= Html::a(
            'View',
            '#',
            [
                'class' => 'dropdown-item btn-modal-show',
                'title' => 'Tasks',
                'data-url' => $viewUrl,
                'data-title' => 'Tasks',
                'data-modal-id' => 'modal-lg',
            ]
        ) ?>
        <?= Html::a(
            'Add',
            '#',
            [
                'class' => 'dropdown-item btn-modal-show',
                'title' => 'Add Task',
                'data-url' => $addUrl,
                'data-title' => 'Add Task',
                'data-modal-id' => 'modal-df',
            ]
        ) ?>
    </div>
</div> &nbsp;
