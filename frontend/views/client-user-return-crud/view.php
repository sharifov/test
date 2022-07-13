<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\clientUserReturn\entity\ClientUserReturn */

$this->title = $model->cur_client_id;
$this->params['breadcrumbs'][] = ['label' => 'Client User Returns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-user-return-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'cur_client_id' => $model->cur_client_id, 'cur_user_id' => $model->cur_user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'cur_client_id' => $model->cur_client_id, 'cur_user_id' => $model->cur_user_id], [
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
            'cur_client_id',
            'cur_user_id',
            'cur_created_dt',
        ],
    ]) ?>

</div>
