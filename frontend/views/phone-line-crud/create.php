<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLine\PhoneLine */

$this->title = 'Create Phone Line';
$this->params['breadcrumbs'][] = ['label' => 'Phone Lines', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-line-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
