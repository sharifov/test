<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteTrip */

$this->title = 'Update Quote Trip: ' . $model->qt_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Trips', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->qt_id, 'url' => ['view', 'qt_id' => $model->qt_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="quote-trip-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
