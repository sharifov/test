<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Email */


?>
<div class="inbox-body">
    <div class="mail_heading row">
        <div class="col-md-8">
            <div class="btn-group">
                <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-reply"></i> Reply</button>
                <?php /*<button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Forward"><i class="fa fa-share"></i></button>
                <button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Print"><i class="fa fa-print"></i></button>*/ ?>
                <button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Trash"><i class="fa fa-trash-o"></i></button>
            </div>
        </div>
        <div class="col-md-4 text-right">
            <p class="date"><i class="fa fa-calendar"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->e_created_dt))?></p>
        </div>
        <div class="col-md-12">
            <h4> <?=Html::encode($model->e_email_subject)?></h4>
        </div>
    </div>
    <div class="sender-info">
        <div class="row">
            <div class="col-md-12">
                From: <strong><i class="fa fa-user"></i></strong>
                <span><?=Html::encode($model->e_email_from)?></span> To:
                <i class="fa fa-user"></i> <strong><?=Html::encode($model->e_email_to)?></strong>
                <a class="sender-dropdown"><i class="fa fa-chevron-down"></i></a>
            </div>
        </div>
    </div>
    <div class="view-mail">
        <object width="100%" height="800" data="<?=\yii\helpers\Url::to(['email/view', 'id' => $model->e_id, 'preview' => 1])?>"></object>
    </div>

    <?/*<div class="attachment">
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
    <?/*<div class="btn-group">
        <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-reply"></i> Reply</button>
        <button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Forward"><i class="fa fa-share"></i></button>
        <button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Print"><i class="fa fa-print"></i></button>
        <button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Trash"><i class="fa fa-trash-o"></i></button>
    </div>*/?>
</div>