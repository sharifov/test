<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserParams */

$this->title = $model->up_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Params', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-params-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->up_user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->up_user_id], [
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
            'up_user_id',
            'upUser.username',
            [
                'attribute' => 'up_commission_percent',
                'value' => function(\common\models\UserParams $model) {
                    return $model->up_base_amount ? '$'.number_format($model->up_base_amount , 2) : '-';
                },
                
            ],

            [
                'attribute' => 'up_base_amount',
                'value' => function(\common\models\UserParams $model) {
                    return $model->up_commission_percent ? $model->up_commission_percent. '%' : '-';
                },

            ],
            'up_updated_dt',
            'up_updated_user_id',
            'upUpdatedUser.username'
        ],
    ]) ?>

</div>
