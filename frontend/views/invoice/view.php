<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = $model->inv_id;
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="invoice-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->inv_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->inv_id], [
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
            'inv_id',
            'inv_gid',
            'inv_uid',
            'inv_order_id',
            'inv_status_id',
            'inv_sum',
            'inv_client_sum',
            'inv_client_currency',
            'inv_currency_rate',
            'inv_description:ntext',
            [
                'attribute' => 'inv_created_user_id',
                'value' => static function(\common\models\Invoice $model){
                    return $model->invCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->invCreatedUser->username) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'inv_updated_user_id',
                'value' => static function(\common\models\Invoice $model){
                    return $model->invUpdatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->invUpdatedUser->username) : '-';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'inv_created_dt',
                'value' => static function(\common\models\Invoice $model) {
                    return $model->inv_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->inv_created_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'inv_updated_dt',
                'value' => static function(\common\models\Invoice $model) {
                    return $model->inv_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->inv_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],
        ],
    ]) ?>

</div>
