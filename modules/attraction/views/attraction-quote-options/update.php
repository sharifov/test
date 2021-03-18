<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\AttractionQuoteOptions */

$this->title = 'Update Attraction Quote Options: ' . $model->atqo_id;
$this->params['breadcrumbs'][] = ['label' => 'Attraction Quote Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->atqo_id, 'url' => ['view', 'id' => $model->atqo_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="attraction-quote-options-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
