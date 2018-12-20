<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Sms */

$this->title = $model->s_id;
$this->params['breadcrumbs'][] = ['label' => 'Sms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->s_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->s_id], [
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
            's_id',
            's_reply_id',
            's_lead_id',
            's_project_id',
            's_phone_from',
            's_phone_to',
            's_sms_text:ntext',
            's_sms_data:ntext',
            's_type_id',
            's_template_type_id',
            's_language_id',
            's_communication_id',
            's_is_deleted',
            's_is_new',
            's_delay',
            's_priority',
            's_status_id',
            's_status_done_dt',
            's_read_dt',
            's_error_message',
            's_tw_price',
            's_tw_sent_dt',
            's_tw_account_sid',
            's_tw_message_sid',
            's_tw_num_segments',
            's_tw_to_country',
            's_tw_to_state',
            's_tw_to_city',
            's_tw_to_zip',
            's_tw_from_country',
            's_tw_from_state',
            's_tw_from_city',
            's_tw_from_zip',
            's_created_user_id',
            's_updated_user_id',
            's_created_dt',
            's_updated_dt',
        ],
    ]) ?>

</div>
