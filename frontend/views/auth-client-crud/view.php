<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\authClient\entity\AuthClient */

$this->title = $model->ac_id;
$this->params['breadcrumbs'][] = ['label' => 'Auth Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="auth-client-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ac_id' => $model->ac_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ac_id' => $model->ac_id], [
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
            'ac_id',
            'ac_user_id:username',
            'ac_source',
            'ac_source_id',
            'ac_email:email',
            'ac_ip',
            'ac_useragent',
            'ac_created_dt',
        ],
    ]) ?>

</div>
