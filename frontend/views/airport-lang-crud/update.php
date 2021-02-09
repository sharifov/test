<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\airportLang\entity\AirportLang */

$this->title = 'Update Airport Lang: ' . $model->ail_iata;
$this->params['breadcrumbs'][] = ['label' => 'Airport Langs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ail_iata, 'url' => ['view', 'ail_iata' => $model->ail_iata, 'ail_lang' => $model->ail_lang]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="airport-lang-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
