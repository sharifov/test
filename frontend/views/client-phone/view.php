<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ClientPhone */

$this->title = $model->phone;
$this->params['breadcrumbs'][] = ['label' => 'Client Phones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-phone-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            'id',
            [
                'attribute' => 'client_id',
                'label' => 'Client',
                'value' => static function ($model) {
                    return '<i class="fa fa-user"></i> ' . ($model->client ? Html::encode($model->client->full_name . ' (id: ' . $model->client->id . ')') : '');
                },
                'format' => 'raw'
            ],
            'phone',
            'cp_title',
            [
                'attribute' => 'created',
                'value' => static function (\common\models\ClientPhone $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'type',
                'value' => static function (\common\models\ClientPhone $model) {
                    return $model::getPhoneTypeLabel($model->type);
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'updated',
                'value' => static function (\common\models\ClientPhone $model) {
                    return $model->updated ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated)) : null;
                },
                'format' => 'raw'
            ],
            'comments:ntext',
            'is_sms:boolean',
            [
                'attribute' => 'validate_dt',
                'value' => static function (\common\models\ClientPhone $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->validate_dt));
                },
                'format' => 'raw'
            ],

        ],
    ]) ?>

</div>
