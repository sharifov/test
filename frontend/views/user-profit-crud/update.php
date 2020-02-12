<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\user\profit\UserProfit */

$this->title = 'Update User Profit: ' . $model->up_id;
$this->params['breadcrumbs'][] = ['label' => 'User Profits', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->up_id, 'url' => ['view', 'id' => $model->up_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-profit-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
