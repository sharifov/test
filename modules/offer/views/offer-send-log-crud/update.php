<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\offer\src\entities\offerSendLog\OfferSendLog */

$this->title = 'Update Offer Send Log: ' . $model->ofsndl_id;
$this->params['breadcrumbs'][] = ['label' => 'Offer Send Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ofsndl_id, 'url' => ['view', 'id' => $model->ofsndl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="offer-send-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
