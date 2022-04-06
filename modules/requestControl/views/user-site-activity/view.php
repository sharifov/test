<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\requestControl\models\UserSiteActivity */

$this->title = $model->usa_id;
$this->params['breadcrumbs'][] = ['label' => 'User Site Activities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-site-activity-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->usa_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->usa_id], [
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
            'usa_id',
            'usa_user_id',
            'usa_request_url:url',
            'usa_page_url:url',
            'usa_ip',
            'usa_request_type',
            'usa_request_get:ntext',
            'usa_request_post:ntext',
            'usa_created_dt',
        ],
    ]) ?>

</div>
