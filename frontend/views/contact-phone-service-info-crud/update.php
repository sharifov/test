<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo */

$this->title = 'Update Contact Phone Service Info: ' . $model->cpsi_cpl_id;
$this->params['breadcrumbs'][] = ['label' => 'Contact Phone Service Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cpsi_cpl_id, 'url' => ['view', 'cpsi_cpl_id' => $model->cpsi_cpl_id, 'cpsi_service_id' => $model->cpsi_service_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="contact-phone-service-info-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
