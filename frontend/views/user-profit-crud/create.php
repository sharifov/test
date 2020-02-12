<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\user\profit\UserProfit */

$this->title = 'Create User Profit';
$this->params['breadcrumbs'][] = ['label' => 'User Profits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-profit-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
