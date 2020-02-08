<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskStatusReason\QaTaskStatusReason */

$this->title = 'Create Qa Task Status Reason';
$this->params['breadcrumbs'][] = ['label' => 'Qa Task Status Reasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qa-task-status-reason-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
