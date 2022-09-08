<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use src\entities\email\Email;
use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\widgets\FileStorageListWidget;

/* @var $this yii\web\View */
/* @var $model src\entities\email\Email */

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
        <h4>Subject: <?= Html::encode($model->emailBody->embd_email_subject) ?></h4>
        <hr>
        <h4>Email preview:</h4>
        <object width="100%" height="1000"
                data="<?= \yii\helpers\Url::to(['email-normalized/view', 'id' => $model->e_id, 'preview' => 1]) ?>"></object>
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
                'reply.e_id',
                'leads:leads',
                'cases:cases',
                'clientsIds:clients',
                'e_project_id:projectName',
                'emailFrom',
                'emailFromName',
                'emailTo',
                'emailToName',
                'emailsCC:list',
                'emailsBCC:list',
                'emailSubject',
                'emailData:array',
                'typeName',
                'templateTypeId',
                'languageId',
                'communicationId',
                'e_is_deleted:boolean',
                'emailLog.el_is_new:boolean',
                'priority',
                'statusName',
                'statusDoneDt',
                'emailLog.el_read_dt',
                'errorMessage',
                'e_created_user_id:usernameWithId',
                'e_updated_user_id:usernameWithId',
                'e_created_dt',
                'e_updated_dt',
                'messageId',
                'emailLog.el_ref_message_id:ntext',
                'emailLog.el_inbox_created_dt',
                'emailLog.el_inbox_email_id',
            ],
        ]) ?>
    </div>
</div>