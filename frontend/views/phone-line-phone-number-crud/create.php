<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLinePhoneNumber\entity\PhoneLinePhoneNumber */

$this->title = 'Create Phone Line Phone Number';
$this->params['breadcrumbs'][] = ['label' => 'Phone Line Phone Numbers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-line-phone-number-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
