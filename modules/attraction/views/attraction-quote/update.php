<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\AttractionQuote */

$this->title = 'Update Attraction Quote: ' . $model->atnq_id;
$this->params['breadcrumbs'][] = ['label' => 'Attraction Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->atnq_id, 'url' => ['view', 'id' => $model->atnq_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="attraction-quote-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
