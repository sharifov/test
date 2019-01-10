<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Notifications */

$this->title = $model->n_title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('notifications', 'My Notifications'), 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notifications-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('notifications', 'My Notifications'), ['list'], ['class' => 'btn btn-success']) ?>

        <?= Html::a(Yii::t('notifications', 'Delete'), ['soft-delete', 'id' => $model->n_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('notifications', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'n_id',
            [
              'attribute' => 'n_type_id',
                'value' => function(\common\models\Notifications $model) {
                    return $model->getType();
                }
            ],
            'n_title',
            'n_message:ntext',
            //'n_new:boolean',
            'n_read_dt',
            'n_created_dt',
        ],
    ]) ?>

</div>
