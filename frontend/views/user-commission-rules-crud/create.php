<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserCommissionRules */

$this->title = 'Create User Commission Rules';
$this->params['breadcrumbs'][] = ['label' => 'User Commission Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-commission-rules-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
