<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo */

$this->title = 'Create Contact Phone Service Info';
$this->params['breadcrumbs'][] = ['label' => 'Contact Phone Service Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-phone-service-info-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
