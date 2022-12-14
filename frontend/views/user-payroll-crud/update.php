<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\user\entity\payroll\UserPayroll */

$this->title = 'Update User Payroll: ' . $model->ups_id;
$this->params['breadcrumbs'][] = ['label' => 'User Payrolls', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ups_id, 'url' => ['view', 'id' => $model->ups_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-payroll-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
