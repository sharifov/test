<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\widgets\FileStorageListWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Email */

$this->title = $model->e_id;
$this->params['breadcrumbs'][] = ['label' => 'Emails', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->e_id], ['class' => 'btn btn-primary']) ?>
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
        <h4>Subject: <?= Html::encode($model->emailSubject)?></h4>
        <hr>
        <h4>Email preview:</h4>
        <object width="100%" height="1000" data="<?= Url::to(['view', 'id' => $model->e_id, 'preview' => 1])?>"></object>
        <?php if (FileStorageSettings::isEnabled()) : ?>
            <?= FileStorageListWidget::byEmail(
                $model->e_id,
                $model->emailData
            ) ?>
        <?php endif; ?>
    </div>

    <div class="col-md-5">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'e_id',
                'e_reply_id',
                'e_lead_id',
                'e_case_id',
                'e_client_id:client',
                'e_project_id:projectName',
                'emailFrom',
                'emailFromName',
                'emailTo',
                'emailToName',
                'e_email_cc',
                'e_email_bc',
                'e_email_subject',
                'e_attach',
                'e_email_data:ntext',
                'typeName',
                'templateTypeId',
                'languageId',
                'communicationId',
                'e_is_deleted:boolean',
                'e_is_new:boolean',
                'e_delay',
                'e_priority',
                'statusName',
                'e_status_done_dt',
                'e_read_dt',
                'e_error_message',
                'e_created_user_id:usernameWithId',
                'e_updated_user_id:usernameWithId',
                'e_created_dt',
                'e_updated_dt',
                'e_message_id',
                'e_ref_message_id:ntext',
                'e_inbox_created_dt',
                'e_inbox_email_id',
            ],
        ]) ?>
    </div>
</div>