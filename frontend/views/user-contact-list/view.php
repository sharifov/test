<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserContactList */

$this->title = $model->ucl_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Contact Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-contact-list-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ucl_user_id' => $model->ucl_user_id, 'ucl_client_id' => $model->ucl_client_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ucl_user_id' => $model->ucl_user_id, 'ucl_client_id' => $model->ucl_client_id], [
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
            'ucl_user_id',
            'ucl_client_id',
            'ucl_title',
            'ucl_description:ntext',
            'ucl_created_dt',
        ],
    ]) ?>

</div>
