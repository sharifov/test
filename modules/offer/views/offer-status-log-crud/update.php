<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\offer\src\entities\offerStatusLog\OfferStatusLog */

$this->title = 'Update Offer Status Log: ' . $model->osl_id;
$this->params['breadcrumbs'][] = ['label' => 'Offer Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->osl_id, 'url' => ['view', 'id' => $model->osl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="offer-status-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
