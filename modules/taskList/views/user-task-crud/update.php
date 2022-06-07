<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\userTask\UserTask */

$this->title = 'Update User Task: ' . $model->ut_id;
$this->params['breadcrumbs'][] = ['label' => 'User Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ut_id, 'url' => ['view', 'ut_id' => $model->ut_id, 'ut_year' => $model->ut_year, 'ut_month' => $model->ut_month]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-task-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
