<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\quoteLabel\entity\QuoteLabel */

$this->title = $model->ql_quote_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Labels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quote-label-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'ql_quote_id' => $model->ql_quote_id, 'ql_label_key' => $model->ql_label_key], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'ql_quote_id' => $model->ql_quote_id, 'ql_label_key' => $model->ql_label_key], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'ql_quote_id',
                'ql_label_key',
            ],
        ]) ?>

    </div>

</div>
