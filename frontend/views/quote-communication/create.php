<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\QuoteCommunication */

$this->title = 'Create Email Quote';
$this->params['breadcrumbs'][] = ['label' => 'Email Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-quote-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
