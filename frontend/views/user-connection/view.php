<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserConnection */

$this->title = $model->uc_id;
$this->params['breadcrumbs'][] = ['label' => 'User Connections', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-connection-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->uc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->uc_id], [
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
            'uc_id',
            'uc_connection_id',
            'uc_user_id',
            'uc_lead_id',
            'uc_case_id',
            'uc_user_agent',
            'uc_controller_id',
            'uc_action_id',
            'uc_page_url:url',
            'uc_ip',
            'uc_created_dt',
            'uc_connection:text'
        ],
    ]) ?>

</div>
