<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use src\helpers\email\MaskEmailHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Email */


?>
<div class="inbox-body">
    <div class="mail_heading row">
        <div class="col-md-8">
            <div class="btn-group">

                <?php if ($model->e_type_id == \common\models\Email::TYPE_DRAFT || $model->e_type_id == \common\models\Email::TYPE_OUTBOX) : ?>
                    <?= Html::a('<i class="fa fa-edit"></i> Edit', \yii\helpers\Url::current(['reply_id' => null, 'id' => null, 'edit_id' => $model->e_id]), [
                        'class' => 'btn btn-sm btn-warning',
                        'data-placement' => 'top',
                        'data-toggle' => 'tooltip',
                        'data-original-title' => 'Edit',
                        /*'data' => [
                            'confirm' => 'Are you sure you want to delete this message?',
                            'method' => 'post',
                        ],*/
                    ]) ?>
                <?php endif; ?>
                <?php if ($model->e_type_id == \common\models\Email::TYPE_INBOX) : ?>
                    <?= Html::a('<i class="fa fa-reply"></i> Reply', \yii\helpers\Url::current(['id' => null, 'reply_id' => $model->e_id, 'edit_id' => null]), [
                        'class' => 'btn btn-sm btn-primary',
                        'data-placement' => 'top',
                        'data-toggle' => 'tooltip',
                        'data-original-title' => 'Reply',
                        /*'data' => [
                            'confirm' => 'Are you sure you want to delete this message?',
                            'method' => 'post',
                        ],*/
                    ]) ?>
                <?php endif; ?>

                <?php /*<button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Forward"><i class="fa fa-share"></i></button>
                <button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Print"><i class="fa fa-print"></i></button>*/ ?>
                <?php /*<button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Trash"><i class="fa fa-trash-o"></i></button>*/ ?>
                <?= Html::a('<i class="fa fa-trash-o"></i>' . ($model->e_is_deleted ? ' UnTrash' : '' ), ['soft-delete', 'id' => $model->e_id], [
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
            <h4> <span class="badge badge-info"><?=$model->getTypeName()?></span> <?=Html::encode($model->e_email_subject)?></h4>
        </div>
    </div>
    <div class="sender-info">
        <div class="row">
            <div class="col-md-12">
                From: <strong><i class="fa fa-user"></i></strong>
                <span><?=Html::encode($model->e_email_from)?></span> To:
                <i class="fa fa-user"></i> <strong><?=Html::encode(MaskEmailHelper::masking($model->e_email_to))?></strong>
                <a class="sender-dropdown"><i class="fa fa-chevron-down"></i></a>
                <?php if ($model->project) :?>
                    <span class="label label-info"><?=Html::encode($model->project->name)?></span>
                <?php endif;?>
                <?php if ($model->e_status_id) :?>
                    <span class="badge badge-warning"><?=Html::encode($model->getStatusName())?></span>
                <?php endif;?>

                <?php if ($model->e_is_deleted) :?>
                    <span class="badge badge-danger">Deleted</span>
                <?php endif;?>

            </div>
        </div>
    </div>
    <div class="view-mail">
        <object width="100%" height="800" data="<?=\yii\helpers\Url::to(['email/view', 'id' => $model->e_id, 'preview' => 1])?>"></object>
    </div>

    <?php /*<div class="attachment">
        <p>
            <span><i class="fa fa-paperclip"></i> 3 attachments — </span>
            <a href="#">Download all attachments</a> |
            <a href="#">View all images</a>
        </p>
        <ul>
            <li>
                <a href="#" class="atch-thumb">
                    <img src="images/inbox.png" alt="img">
                </a>

                <div class="file-name">
                    image-name.jpg
                </div>
                <span>12KB</span>


                <div class="links">
                    <a href="#">View</a> -
                    <a href="#">Download</a>
                </div>
            </li>

            <li>
                <a href="#" class="atch-thumb">
                    <img src="images/inbox.png" alt="img">
                </a>

                <div class="file-name">
                    img_name.jpg
                </div>
                <span>40KB</span>

                <div class="links">
                    <a href="#">View</a> -
                    <a href="#">Download</a>
                </div>
            </li>
            <li>
                <a href="#" class="atch-thumb">
                    <img src="images/inbox.png" alt="img">
                </a>

                <div class="file-name">
                    img_name.jpg
                </div>
                <span>30KB</span>

                <div class="links">
                    <a href="#">View</a> -
                    <a href="#">Download</a>
                </div>
            </li>

        </ul>
    </div>*/?>
    <?php /*<div class="btn-group">
        <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-reply"></i> Reply</button>
        <button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Forward"><i class="fa fa-share"></i></button>
        <button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Print"><i class="fa fa-print"></i></button>
        <button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Trash"><i class="fa fa-trash-o"></i></button>
    </div>*/?>
</div>