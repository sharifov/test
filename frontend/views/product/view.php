<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = $model->pr_id;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->pr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->pr_id], [
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
            'pr_id',
            'pr_type_id',
            'pr_name',
            'pr_lead_id',
            'pr_description:ntext',
            'pr_status_id',
            'pr_service_fee_percent',
            'pr_created_user_id',
            'pr_updated_user_id',
            [
                'attribute' => 'pr_created_dt',
                'value' => static function(\common\models\Product $model) {
                    return $model->pr_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->pr_created_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'pr_updated_dt',
                'value' => static function(\common\models\Product $model) {
                    return $model->pr_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->pr_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],
        ],
    ]) ?>

</div>
