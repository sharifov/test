<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientData\entity\ClientData */

$this->title = 'Update Client Data: ' . $model->cd_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cd_id, 'url' => ['view', 'id' => $model->cd_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-data-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
