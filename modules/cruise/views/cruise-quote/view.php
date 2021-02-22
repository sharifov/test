<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruiseQuote\CruiseQuote */

$this->title = $model->crq_id;
$this->params['breadcrumbs'][] = ['label' => 'Cruise Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cruise-quote-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->crq_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->crq_id], [
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
                'crq_id',
                'crq_hash_key',
                'crq_product_quote_id',
                'crq_cruise_id',
                'crq_amount',
                'crq_amount_per_person',
                'crq_currency',
                'crq_adults',
                'crq_children',
                'crq_system_mark_up',
                'crq_agent_mark_up',
                'crq_service_fee_percent',
            ],
        ]) ?>


    </div>
<div class="col-md-12">
<pre>
<?php
\yii\helpers\VarDumper::dump($model->crq_data_json);
?>
</pre>
</div>
</div>
