<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductQuoteOption */

$this->title = 'Update Product Quote Option: ' . $model->pqo_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pqo_id, 'url' => ['view', 'id' => $model->pqo_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-quote-option-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
