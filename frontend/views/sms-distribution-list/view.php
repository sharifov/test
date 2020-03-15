<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\sms\entity\smsDistributionList\SmsDistributionList */

$this->title = $model->sdl_id;
$this->params['breadcrumbs'][] = ['label' => 'Sms Distribution Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sms-distribution-list-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->sdl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->sdl_id], [
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
            'sdl_id',
            'sdl_com_id',
            'sdl_project_id',
            'sdl_phone_from',
            'sdl_phone_to',
            'sdl_client_id',
            'sdl_text:ntext',
            'sdl_start_dt',
            'sdl_end_dt',
            'sdl_status_id',
            'sdl_priority',
            'sdl_error_message:ntext',
            'sdl_message_sid',
            'sdl_num_segments',
            'sdl_price',
            'sdl_created_user_id',
            'sdl_updated_user_id',
            'sdl_created_dt',
            'sdl_updated_dt',
        ],
    ]) ?>

</div>
