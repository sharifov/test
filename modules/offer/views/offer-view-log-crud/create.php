<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\offer\src\entities\offerViewLog\OfferViewLog */

$this->title = 'Create Offer View Log';
$this->params['breadcrumbs'][] = ['label' => 'Offer View Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-view-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
