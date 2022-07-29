<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\InfoBlock */

$this->title = $model->ib_title;
$this->params['breadcrumbs'][] = ['label' => 'Info Block', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="info-block-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ib_id' => $model->ib_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ib_id' => $model->ib_id], [
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
            'ib_id',
            'ib_title',
            'ib_key',
            'ib_description:ntext',
            'ib_enabled:boolean',
            [
                'attribute' => 'ib_created_user_id',
                'value' => static function (\common\models\InfoBlock $model) {
                    return $model->createdUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->createdUser->username) : $model->ib_created_user_id;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'ib_updated_user_id',
                'value' => static function (\common\models\InfoBlock $model) {
                    return $model->updatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->updatedUser->username) : $model->ib_updated_user_id;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'ib_created_dt',
                'value' => static function (\common\models\InfoBlock $model) {
                    return $model->ib_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->ib_created_dt)) : '-';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'ib_updated_dt',
                'value' => static function (\common\models\InfoBlock $model) {
                    return $model->ib_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->ib_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],
        ],
    ]) ?>

</div>
