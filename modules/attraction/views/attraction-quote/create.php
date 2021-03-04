<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\AttractionQuote */

$this->title = 'Create Attraction Quote';
$this->params['breadcrumbs'][] = ['label' => 'Attraction Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hotel-quote-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
