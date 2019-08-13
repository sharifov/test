<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DepartmentPhoneProject */

$this->title = 'Create Department Phone Project';
$this->params['breadcrumbs'][] = ['label' => 'Department Phone Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-phone-project-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
