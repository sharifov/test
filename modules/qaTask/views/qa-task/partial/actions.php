<?php

use modules\qaTask\src\abac\QaTaskAbacObject;

/* @var $model modules\qaTask\src\entities\qaTask\QaTask */
/* @var $this yii\web\View */

?>

<?= $this->render('actions/status_history', ['model' => $model]) ?>
<?= $this->render('actions/take', ['model' => $model]) ?>
<?= $this->render('actions/take_over', ['model' => $model]) ?>
<?= $this->render('actions/escalate', ['model' => $model]) ?>
<?= $this->render('actions/close', ['model' => $model]) ?>
<?= $this->render('actions/cancel', ['model' => $model]) ?>
<?= $this->render('actions/return', ['model' => $model]) ?>
<?= $this->render('actions/decide', ['model' => $model]) ?>

<?php /** @abac null, QaTaskAbacObject::ACT_USER_ASSIGN, QaTaskAbacObject::ACTION_ACCESS, Assign Multiple Tasks To QA*/ ?>
<?php if (Yii::$app->abac->can(null, QaTaskAbacObject::ACT_USER_ASSIGN, QaTaskAbacObject::ACTION_ACCESS)) : ?>
    <?= $this->render('actions/user_assign', ['model' => $model]) ?>
<?php endif; ?>

<?php
