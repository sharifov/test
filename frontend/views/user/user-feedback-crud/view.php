<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\user\userFeedback\entity\UserFeedback */

$this->title = $model->uf_id;
$this->params['breadcrumbs'][] = ['label' => 'User Feedbacks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-feedback-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'uf_id' => $model->uf_id, 'uf_created_dt' => $model->uf_created_dt], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'uf_id' => $model->uf_id, 'uf_created_dt' => $model->uf_created_dt], [
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
            'uf_id',
            'uf_type_id',
            'uf_status_id',
            'uf_title',
            'uf_message:ntext',
            'uf_data_json',
            'uf_created_dt',
            'uf_updated_dt',
            'uf_created_user_id',
            'uf_updated_user_id',
        ],
    ]) ?>

</div>
