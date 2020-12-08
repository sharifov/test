<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientAccountSocial\entity\ClientAccountSocial */

$this->title = 'Update Client Account Social: ' . $model->cas_ca_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Account Socials', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cas_ca_id, 'url' => ['view', 'cas_ca_id' => $model->cas_ca_id, 'cas_type_id' => $model->cas_type_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-account-social-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
