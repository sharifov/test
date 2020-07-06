<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EmailUnsubscribe */

$this->title = 'Create Email Unsubscribe';
$this->params['breadcrumbs'][] = ['label' => 'Email Unsubscribes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-unsubscribe-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('partial/_form', [
        'model' => $model,
    ]) ?>

</div>
