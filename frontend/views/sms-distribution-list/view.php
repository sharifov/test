<?php

use common\components\grid\DateTimeColumn;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\sms\entity\smsDistributionList\SmsDistributionList */

$this->title = 'SMS Distribution ID: ' . $model->sdl_id;
$this->params['breadcrumbs'][] = ['label' => 'Sms Distribution Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sms-distribution-list-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-edit"></i> Update', ['update', 'id' => $model->sdl_id], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> Delete', ['delete', 'id' => $model->sdl_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'sdl_id',
            'sdl_com_id',
            'sdl_project_id',
            'sdl_phone_from',
            'sdl_phone_to',
            'sdl_client_id:client',
            'sdl_text:ntext',
            'sdl_start_dt:byUserDateTime',
            'sdl_end_dt:byUserDateTime',
        ],
    ]) ?>
    </div>
    <div class="col-md-6">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'sdl_status_id',
                'sdl_priority',
                'sdl_error_message:ntext',
                'sdl_message_sid',
                'sdl_num_segments',
                'sdl_price',

                'sdl_created_user_id:userName',
                'sdl_created_dt:byUserDateTime',

                'sdl_updated_user_id:userName',
                'sdl_updated_dt:byUserDateTime',

            ],
        ]) ?>
    </div>

</div>
