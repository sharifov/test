<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruiseQuote\CruiseQuote */

$this->title = 'Update Cruise Quote: ' . $model->crq_id;
$this->params['breadcrumbs'][] = ['label' => 'Cruise Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->crq_id, 'url' => ['view', 'id' => $model->crq_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cruise-quote-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
