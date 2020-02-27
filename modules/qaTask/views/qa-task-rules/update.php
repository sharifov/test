<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskRules\QaTaskRules */

$this->title = 'Update Qa Task Rules: ' . $model->tr_id;
$this->params['breadcrumbs'][] = ['label' => 'Qa Task Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->tr_id, 'url' => ['view', 'id' => $model->tr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="qa-task-rules-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
