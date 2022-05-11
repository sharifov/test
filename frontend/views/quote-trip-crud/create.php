<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteTrip */

$this->title = 'Create Quote Trip';
$this->params['breadcrumbs'][] = ['label' => 'Quote Trips', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-trip-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
