<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelQuoteServiceLog */

$this->title = $model->hqsl_id;
$this->params['breadcrumbs'][] = ['label' => 'Hotel Quote Service Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="hotel-quote-service-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->hqsl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->hqsl_id], [
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
            'hqsl_id',
            'hqsl_hotel_quote_id',
            'hqsl_action_type_id',
            'hqsl_status_id',
            'hqsl_message:ntext',
            'hqsl_created_user_id',
            'hqsl_created_dt',
            'hqsl_updated_dt',
        ],
    ]) ?>

</div>
