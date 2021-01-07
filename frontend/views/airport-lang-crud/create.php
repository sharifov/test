<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\airportLang\entity\AirportLang */

$this->title = 'Create Airport Lang';
$this->params['breadcrumbs'][] = ['label' => 'Airport Langs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="airport-lang-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
