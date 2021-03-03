<?php

use modules\order\src\processManager\OrderProcessManager;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model OrderProcessManager */

$this->title = 'Create Order Process Manager';
$this->params['breadcrumbs'][] = ['label' => 'Orders Process Managers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
