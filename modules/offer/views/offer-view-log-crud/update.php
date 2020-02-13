<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\offer\src\entities\offerViewLog\OfferViewLog */

$this->title = 'Update Offer View Log: ' . $model->ofvwl_id;
$this->params['breadcrumbs'][] = ['label' => 'Offer View Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ofvwl_id, 'url' => ['view', 'id' => $model->ofvwl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="offer-view-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
