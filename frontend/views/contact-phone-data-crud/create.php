<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\contactPhoneData\entity\ContactPhoneData */

$this->title = 'Create Contact Phone Data';
$this->params['breadcrumbs'][] = ['label' => 'Contact Phone Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-phone-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
