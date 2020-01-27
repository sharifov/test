<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteStatusLog\ProductQuoteStatusLog */

$this->title = $model->pqsl_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-quote-status-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->pqsl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->pqsl_id], [
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
            'pqsl_id',
            'productQuote:productQuote',
            'pqsl_start_status_id:productQuoteStatus',
            'pqsl_end_status_id:productQuoteStatus',
            'pqsl_start_dt:byUserDateTime',
            'pqsl_end_dt:byUserDateTime',
            'pqsl_duration',
            'pqsl_description',
            'pqsl_owner_user_id:userName',
            'pqsl_created_user_id:userName',
        ],
    ]) ?>

</div>
