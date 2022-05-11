<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\taskList\TaskList */

$this->title = 'Update Task List: ' . $model->tl_id;
$this->params['breadcrumbs'][] = ['label' => 'Task Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->tl_id, 'url' => ['view', 'tl_id' => $model->tl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="task-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
