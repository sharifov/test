<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ConferenceRoom */

$this->title = $model->cr_name;
$this->params['breadcrumbs'][] = ['label' => 'Conference Rooms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="conference-room-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->cr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->cr_id], [
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
            'cr_id',
            'cr_key',
            'cr_name',
            'cr_phone_number',
            'cr_enabled:boolean',
            'cr_start_dt',
            'cr_end_dt',
            'cr_param_muted:boolean',
            'cr_param_beep',
            'cr_param_start_conference_on_enter:boolean',
            'cr_param_end_conference_on_exit:boolean',
            'cr_param_max_participants',
            'cr_param_record',
            'cr_param_region',
            'cr_param_trim',
            'cr_param_wait_url:url',
            'cr_moderator_phone_number',
            'cr_welcome_message:ntext',
            'cr_created_dt:datetime',
            'cr_updated_dt',
            'cr_created_user_id',
            'cr_updated_user_id',
        ],
    ]) ?>
    </div>

</div>
