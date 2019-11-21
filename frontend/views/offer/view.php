<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Offer */

$this->title = $model->of_id;
$this->params['breadcrumbs'][] = ['label' => 'Offers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="offer-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->of_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->of_id], [
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
            'of_id',
            'of_gid',
            'of_uid',
            'of_name',
            'of_lead_id',
            'of_status_id',
            'of_owner_user_id',
            'of_created_user_id',
            'of_updated_user_id',
            [
                'attribute' => 'of_created_dt',
                'value' => static function(\common\models\Offer $model) {
                    return $model->of_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->of_created_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'of_updated_dt',
                'value' => static function(\common\models\Offer $model) {
                    return $model->of_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->of_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],
        ],
    ]) ?>

</div>
