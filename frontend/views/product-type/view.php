<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ProductType */

$this->title = $model->pt_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-type-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->pt_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->pt_id], [
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
            'pt_id',
            'pt_key',
            'pt_name',
            'pt_description:ntext',
            'pt_settings',
            'pt_enabled',
            [
                'attribute' => 'pt_created_dt',
                'value' => static function (\common\models\ProductType $model) {
                    return $model->pt_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->pt_created_dt)) : '-';
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'pt_updated_dt',
                'value' => static function (\common\models\ProductType $model) {
                    return $model->pt_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->pt_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],
        ],
    ]) ?>

</div>
