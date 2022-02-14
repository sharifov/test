<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\phoneNumberRedial\entity\PhoneNumberRedial */

$this->title = 'Create Phone Number Redial';
$this->params['breadcrumbs'][] = ['label' => 'Phone Number Redials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-number-redial-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
