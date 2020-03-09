<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserOnline */

$this->title = 'Create User Online';
$this->params['breadcrumbs'][] = ['label' => 'User Onlines', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-online-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
