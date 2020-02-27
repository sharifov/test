<?php

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

<?php
