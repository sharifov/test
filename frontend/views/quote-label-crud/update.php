<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\quoteLabel\entity\QuoteLabel */

$this->title = 'Update Quote Label: ' . $model->ql_quote_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Labels', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ql_quote_id, 'url' => ['view', 'ql_quote_id' => $model->ql_quote_id, 'ql_label_key' => $model->ql_label_key]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="quote-label-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
