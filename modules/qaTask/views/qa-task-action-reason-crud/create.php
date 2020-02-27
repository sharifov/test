<?php

use modules\qaTask\src\entities\qaTaskActionReason\QaTaskActionReason;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model QaTaskActionReason */

$this->title = 'Create Qa Task Action Reason';
$this->params['breadcrumbs'][] = ['label' => 'Qa Task Action Reasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qa-task-action-reason-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
