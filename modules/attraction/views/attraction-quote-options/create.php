<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\AttractionQuoteOptions */

$this->title = 'Create Attraction Quote Options';
$this->params['breadcrumbs'][] = ['label' => 'Attraction Quote Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attraction-quote-options-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
