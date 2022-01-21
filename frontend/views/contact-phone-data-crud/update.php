<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\contactPhoneData\entity\ContactPhoneData */

$this->title = 'Update Contact Phone Data: ' . $model->cpd_cpl_id;
$this->params['breadcrumbs'][] = ['label' => 'Contact Phone Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cpd_cpl_id, 'url' => ['view', 'cpd_cpl_id' => $model->cpd_cpl_id, 'cpd_key' => $model->cpd_key]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="contact-phone-data-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
