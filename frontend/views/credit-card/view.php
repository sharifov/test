<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CreditCard */

$this->title = $model->cc_id;
$this->params['breadcrumbs'][] = ['label' => 'Credit Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="credit-card-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->cc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->cc_id], [
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
            'cc_id',
            'cc_number',
            'cc_display_number',
            'cc_holder_name',
            'cc_expiration_month',
            'cc_expiration_year',
            'cc_cvv',
            'cc_type_id',
            'cc_status_id',
            'cc_is_expired',
            'cc_created_user_id',
            'cc_updated_user_id',
            'cc_created_dt',
            'cc_updated_dt',
        ],
    ]) ?>

</div>
