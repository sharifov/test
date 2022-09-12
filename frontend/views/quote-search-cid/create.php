<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSearchCid */

$this->title = 'Create Quote Search Cid';
$this->params['breadcrumbs'][] = ['label' => 'Quote Search Cids', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-search-cid-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
