<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\payroll\UserPayroll */

$this->title = 'Create User Payroll';
$this->params['breadcrumbs'][] = ['label' => 'User Payrolls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-payroll-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
