<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\offer\src\entities\offer\Offer */

$this->title = 'Update Offer: ' . $model->of_id;
$this->params['breadcrumbs'][] = ['label' => 'Offers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->of_id, 'url' => ['view', 'id' => $model->of_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="offer-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
