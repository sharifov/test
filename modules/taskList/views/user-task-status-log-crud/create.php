<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\userTask\UserTaskStatusLog */

$this->title = 'Create User Task Status Log';
$this->params['breadcrumbs'][] = ['label' => 'User Task Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-task-status-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
