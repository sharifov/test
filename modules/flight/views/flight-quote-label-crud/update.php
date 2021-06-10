<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\src\entities\flightQuoteLabel\FlightQuoteLabel */

$this->title = 'Update Flight Quote Label: ' . $model->fql_quote_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Labels', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fql_quote_id, 'url' => ['view', 'fql_quote_id' => $model->fql_quote_id, 'fql_label_key' => $model->fql_label_key]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-quote-label-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
