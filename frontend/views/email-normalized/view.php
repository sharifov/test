<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use src\helpers\email\MaskEmailHelper;
use src\entities\email\Email;

/* @var $this yii\web\View */
/* @var $model src\entities\email\Email */

$this->title = $model->e_id;
$this->params['breadcrumbs'][] = ['label' => 'Emails', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Delete', ['delete', 'id' => $model->e_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>


    <div class="col-md-7">

            <hr>
            <h4>Subject: <?=Html::encode($model->emailBody->embd_email_subject)?></h4>
            <hr>
            <h4>Email preview:</h4>
            <object width="100%" height="1000" data="<?=\yii\helpers\Url::to(['email-normalized/view', 'id' => $model->e_id, 'preview' => 1])?>"></object>

    </div>

    <div class="col-md-5">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'e_id',
                'reply.e_id',
                'leads:leads',
                'cases:cases',
                'e_project_id',
                'emailFrom',
                'contactFrom.ea_name',
                'emailTo',
                'contactTo.ea_name',
                'e_email_cc:email',
                'e_email_bc:email',
                'emailBody.embd_email_subject',
                'emailBody.embd_email_data:ntext',
                'e_type_id',
                'params.ep_template_type_id',
                'params.ep_language_id',
                'emailLog.el_communication_id',
                'e_is_deleted',
                'emailLog.el_is_new',
                'params.ep_priority',
                'e_status_id',
                'emailLog.el_status_done_dt',
                'emailLog.el_read_dt',
                'emailLog.el_error_message',
                'e_created_user_id',
                'e_created_dt',
                'emailLog.el_message_id',
                'emailLog.el_ref_message_id:ntext',
                'emailLog.el_inbox_created_dt',
                'emailLog.el_inbox_email_id:email',
            ],
        ]) ?>
    </div>



</div>
