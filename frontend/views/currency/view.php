<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Currency */

$this->title = $model->cur_code;
$this->params['breadcrumbs'][] = ['label' => 'Currencies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="currency-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->cur_code], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->cur_code], [
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
            'cur_code',
            'cur_name',
            'cur_symbol',
            'cur_rate',
            'cur_system_rate',
            'cur_enabled',
            'cur_default',
            'cur_sort_order',
            [
                'attribute' => 'cur_synch_dt',
                'value' => static function(\common\models\Currency $model) {
                    return $model->cur_synch_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->cur_synch_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'cur_created_dt',
                'value' => static function(\common\models\Currency $model) {
                    return $model->cur_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->cur_created_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'cur_updated_dt',
                'value' => static function(\common\models\Currency $model) {
                    return $model->cur_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->cur_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],
        ],
    ]) ?>

</div>
