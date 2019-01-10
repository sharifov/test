<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Notifications */

$this->title = $model->n_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('notifications', 'Notifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notifications-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('notifications', 'Update'), ['update', 'id' => $model->n_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('notifications', 'Delete'), ['delete', 'id' => $model->n_id], [
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
            'n_user_id',
            'n_type_id',
            'n_title',
            'n_message:ntext',
            'n_new:boolean',
            'n_deleted:boolean',
            'n_popup:boolean',
            'n_popup_show:boolean',
            'n_read_dt',
            'n_created_dt',
        ],
    ]) ?>

</div>
