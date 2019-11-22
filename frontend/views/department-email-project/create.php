<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DepartmentEmailProject */

$this->title = 'Create Department Email Project';
$this->params['breadcrumbs'][] = ['label' => 'Department Email Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-email-project-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
