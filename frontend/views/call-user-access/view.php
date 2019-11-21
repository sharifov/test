<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CallUserAccess */

$this->title = $model->cua_call_id;
$this->params['breadcrumbs'][] = ['label' => 'Call User Accesses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-user-access-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'cua_call_id' => $model->cua_call_id, 'cua_user_id' => $model->cua_user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'cua_call_id' => $model->cua_call_id, 'cua_user_id' => $model->cua_user_id], [
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
            'cua_call_id',
            //'cua_user_id',
            [
                'attribute' => 'cua_user_id',
                'value' => static function (\common\models\CallUserAccess $model) {
                    return $model->cuaUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->cuaUser->username) : '-';
                },
                'format' => 'raw',
            ],
            //'cua_status_id',
            [
                'attribute' => 'cua_status_id',
                'value' => static function (\common\models\CallUserAccess $model) {
                    return Html::encode($model->getStatusTypeName());
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'cua_created_dt',
                'value' => static function (\common\models\CallUserAccess $model) {
                    return $model->cua_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->cua_created_dt)) : '-';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'cua_updated_dt',
                'value' => static function (\common\models\CallUserAccess $model) {
                    return $model->cua_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->cua_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],
        ],
    ]) ?>

</div>
