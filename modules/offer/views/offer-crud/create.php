<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\offer\src\entities\offer\Offer */

$this->title = 'Create Offer';
$this->params['breadcrumbs'][] = ['label' => 'Offers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
