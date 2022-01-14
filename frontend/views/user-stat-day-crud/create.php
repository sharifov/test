<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\userStatDay\entity\UserStatDay */

$this->title = 'Create User Stat Day';
$this->params['breadcrumbs'][] = ['label' => 'User Stat Days', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-stat-day-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
