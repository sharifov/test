<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientAccount\entity\ClientAccount */

$this->title = 'Update Client Account: ' . $model->ca_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ca_id, 'url' => ['view', 'id' => $model->ca_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-account-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
