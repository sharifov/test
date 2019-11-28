<?php

use common\models\ClientEmail;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ClientEmail */

$this->title = $model->email;
$this->params['breadcrumbs'][] = ['label' => 'Client Emails', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-email-view">

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
            'email:email',
            [
                'attribute' => 'client_id',
                'label' => 'Client',
                'value' => static function (ClientEmail $model) {
                    return '<i class="fa fa-user"></i> ' . ($model->client ? Html::encode($model->client->full_name . ' (id: '.$model->client->id.')') : '');
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'created',
                'value' => static function (ClientEmail $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'updated',
                'value' => static function (ClientEmail $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated));
                },
                'format' => 'raw'
            ],
            'comments:ntext',
            [
                'attribute' => 'type',
                'value' => static function (ClientEmail $model) {
                    return $model::getEmailTypeLabel($model->type);
                },
                'format' => 'html'
            ],
        ],
    ]) ?>

</div>
