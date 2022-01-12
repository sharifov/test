<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\flightQuoteLabelList\entity\FlightQuoteLabelList */

$this->title = 'Update Flight Quote Label: ' . $model->fqll_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Labels', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fqll_id, 'url' => ['view', 'id' => $model->fqll_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-quote-label-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
