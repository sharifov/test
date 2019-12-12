<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Setting */

$this->title = $model->s_name;
$this->params['breadcrumbs'][] = ['label' => 'Settings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="setting-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->s_id], ['class' => 'btn btn-primary']) ?>
        <?/*= Html::a('Delete', ['delete', 'id' => $model->s_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'s_id',
            's_key',
            //'s_name',
            's_type',
            's_value',
            [
                'attribute' => 's_updated_user_id',
                'value' => static function (\common\models\Setting $model) {
                    return ($model->sUpdatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->sUpdatedUser->username) : $model->s_updated_user_id);
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 's_updated_dt',
                'value' => static function (\common\models\Setting $model) {
                    return $model->s_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->s_updated_dt)) : $model->s_updated_dt;
                },
                'format' => 'raw'
            ],

        ],
    ]) ?>

</div>
