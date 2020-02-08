<?php

use modules\qaTask\src\entities\qaTaskStatusLog\QaTaskStatusLog;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model QaTaskStatusLog */

$this->title = 'Create Qa Task Status Log';
$this->params['breadcrumbs'][] = ['label' => 'Qa Task Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qa-task-status-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
