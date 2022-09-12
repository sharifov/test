<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSearchCid */

$this->title = 'Update Quote Search Cid: ' . $model->qsc_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Search Cids', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->qsc_id, 'url' => ['view', 'qsc_id' => $model->qsc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="quote-search-cid-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
