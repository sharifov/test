<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PhoneBlacklistLog */

$this->title = 'Create Phone Blacklist Log';
$this->params['breadcrumbs'][] = ['label' => 'Phone Blacklist Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-blacklist-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
