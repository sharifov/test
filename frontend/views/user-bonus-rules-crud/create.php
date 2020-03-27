<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserBonusRules */

$this->title = 'Create User Bonus Rules';
$this->params['breadcrumbs'][] = ['label' => 'User Bonus Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-bonus-rules-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
