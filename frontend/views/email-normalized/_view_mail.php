<?php

use yii\helpers\Html;
use src\entities\email\helpers\EmailType;
use yii\helpers\Url;
use src\entities\email\helpers\EmailStatus;

/* @var $this yii\web\View */
/* @var $model src\entities\email\Email */


?>
<div class="inbox-body">
    <div class="mail_heading row">
        <div class="col-md-8">
            <div class="btn-group">

                <?php if (EmailType::isDraftOrOutbox($model->e_type_id)) : ?>
                    <?= Html::a('<i class="fa fa-edit"></i> Edit', Url::current(['action' => 'update', 'id' => $model->e_id]), [
                        'class' => 'btn btn-sm btn-warning',
                        'data-placement' => 'top',
                        'data-toggle' => 'tooltip',
                        'data-original-title' => 'Edit',
                    ]) ?>
                <?php endif; ?>
                <?php if (EmailType::isInbox($model->e_type_id)) : ?>
                    <?= Html::a('<i class="fa fa-reply"></i> Reply', Url::current(['action' => 'reply', 'id' => $model->e_id]), [
                        'class' => 'btn btn-sm btn-primary',
                        'data-placement' => 'top',
                        'data-toggle' => 'tooltip',
                        'data-original-title' => 'Reply',
                    ]) ?>
                <?php endif; ?>

                 <?= Html::a('<i class="fa fa-trash-o"></i>' . ($model->isDeleted() ? ' UnTrash' : '' ), ['soft-delete', 'id' => $model->e_id], [
                    'class' => 'btn btn-sm btn-default',
                    'data-placement' => 'top',
                    'data-toggle' => 'tooltip',
                    'data-original-title' => 'Trash',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this message?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        </div>
        <div class="col-md-4 text-right">
            <p class="date"><i class="fa fa-calendar"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->e_created_dt))?></p>
        </div>
        <div class="col-md-12">
            <h4> <span class="badge badge-info"><?= EmailType::getName($model->e_type_id)?></span> <?= Html::encode($model->emailSubject)?></h4>
        </div>
    </div>
    <div class="sender-info">
        <div class="row">
            <div class="col-md-12">
                From: <strong><i class="fa fa-user"></i></strong>
                <span><?= Html::encode($model->emailFrom)?></span> To:
                <i class="fa fa-user"></i> <strong><?= Html::encode($model->emailTo)?></strong>
                <a class="sender-dropdown"><i class="fa fa-chevron-down"></i></a>
                <?php if ($model->project) :?>
                    <span class="label label-info"><?= Html::encode($model->project->name)?></span>
                <?php endif;?>
                <?php if ($model->e_status_id) :?>
                    <span class="badge badge-warning"><?= Html::encode(EmailStatus::getName($model->e_status_id))?></span>
                <?php endif;?>

                <?php if ($model->isDeleted()) :?>
                    <span class="badge badge-danger">Deleted</span>
                <?php endif;?>

            </div>
        </div>
    </div>
    <div class="view-mail">
        <object width="100%" height="800" data="<?= Url::to(['view', 'id' => $model->e_id, 'preview' => 1])?>"></object>
    </div>
</div>