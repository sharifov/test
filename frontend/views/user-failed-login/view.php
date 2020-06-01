<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\UserFailedLogin */

$this->title = $model->ufl_id;
$this->params['breadcrumbs'][] = ['label' => 'User Failed Logins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-failed-login-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ufl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ufl_id], [
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
            'ufl_id',
            'ufl_username',
            'ufl_user_id',
            'ufl_ua',
            'ufl_ip',
            'ufl_active:booleanByLabel',
            'ufl_session_id',
            'ufl_created_dt',
        ],
    ]) ?>

</div>
