<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\offer\src\entities\offerSendLog\OfferSendLog */

$this->title = 'Create Offer Send Log';
$this->params['breadcrumbs'][] = ['label' => 'Offer Send Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-send-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
