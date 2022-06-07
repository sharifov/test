<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\userTask\UserTask */

$this->title = 'Create User Task';
$this->params['breadcrumbs'][] = ['label' => 'User Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-task-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
