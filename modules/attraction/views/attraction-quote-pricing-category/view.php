<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\AttractionQuotePricingCategory */

$this->title = $model->atqpc_id;
$this->params['breadcrumbs'][] = ['label' => 'Attraction Quote Pricing Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="attraction-quote-pricing-category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->atqpc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->atqpc_id], [
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
            'atqpc_id',
            'atqpc_attraction_quote_id',
            'atqpc_category_id',
            'atqpc_label',
            'atqpc_min_age',
            'atqpc_max_age',
            'atqpc_min_participants',
            'atqpc_max_participants',
            'atqpc_quantity',
            'atqpc_price',
            'atqpc_currency',
            'atqpc_system_mark_up',
            'atqpc_agent_mark_up',
        ],
    ]) ?>

</div>
