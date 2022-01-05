<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\voip\phoneDevice\device\PhoneDevice */

$this->title = 'Create Phone Device';
$this->params['breadcrumbs'][] = ['label' => 'Phone Devices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-device-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
