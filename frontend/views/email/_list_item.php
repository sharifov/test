<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use src\helpers\email\MaskEmailHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Email */
/* @var $modelEmailView common\models\Email */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>

<?php if ($modelEmailView && $modelEmailView->e_id == $model->e_id) :?>
<div style="padding: 8px; background-color: rgba(175,255,236,0.5); color: darkgreen">
<?php else : ?>
<a style="color: <?=($model->e_is_deleted ? 'darkred' : ($model->e_is_new ? 'blue' : 'black'))?>" href="<?=\yii\helpers\Url::current(['id' => $model->e_id, 'action' => null, 'edit_id' => null, 'reply_id' => null ])?>" class="view_email" data-email-id="<?=$model->e_id?>">
<?php endif; ?>

    <div class="mail_list">
        <div class="left">
            <?php if ($model->e_type_id == \common\models\Email::TYPE_DRAFT) :?>
                <i class="fa fa-edit" title="Draft"></i><br>
            <?php endif; ?>
            <?php if ($model->e_type_id == \common\models\Email::TYPE_OUTBOX) :?>
                <i class="fa fa-arrow-circle-up" title="Outbox"></i><br>
            <?php endif; ?>
            <?php if ($model->e_type_id == \common\models\Email::TYPE_INBOX) :?>
                <i class="fa fa-arrow-circle-down" title="Inbox"></i><br>
            <?php endif; ?>

            <?php if ($model->e_is_deleted) :?>
                <i class="fa fa-trash" title="Trash"></i><br>
            <?php endif; ?>

            <?php if ($model->e_is_new) :?>
                <i class="fa fa-circle" title="New message"></i><br>
            <?php endif; ?><?php /*<i class="fa fa-edit"></i>*/?>
        </div>
        <div class="right">
            <h3>
                <?=Html::encode($model->e_email_from)?> - <?=Html::encode(MaskEmailHelper::masking($model->e_email_to))?>
                <small>
                    <?php if ($model->e_type_id == \common\models\Email::TYPE_INBOX) :?>
                        <?=$model->e_inbox_email_id ? 'cid: ' . $model->e_inbox_email_id : ''?><br/>
                        <?=$model->e_inbox_created_dt ? Yii::$app->formatter->asDatetime(strtotime($model->e_inbox_created_dt)) : '-'?>
                    <?php endif; ?>
                    <?php if ($model->e_type_id == \common\models\Email::TYPE_OUTBOX) :?>
                        <?=Yii::$app->formatter->asDatetime(strtotime($model->e_created_dt))?>
                    <?php endif; ?>
                    <?php if ($model->e_type_id == \common\models\Email::TYPE_DRAFT) :?>
                        <i><?=Yii::$app->formatter->asDatetime(strtotime($model->e_created_dt))?></i>
                    <?php endif; ?>

                </small>
            </h3>
            <p><?php if ($model->project) :?>
                <span class="label label-info"><?=Html::encode($model->project->name)?></span>
               <?php endif;?><?=Html::encode($model->e_email_subject)?></p>
        </div>
    </div>
<?php if ($modelEmailView && $modelEmailView->e_id == $model->e_id) :?>
    </div>

<?php else : ?>
</a>
<?php endif; ?>

<?php
/*
 * <a href="#">
                                <div class="mail_list">
                                    <div class="left">
                                        <i class="fa fa-circle"></i> <i class="fa fa-edit"></i>
                                    </div>
                                    <div class="right">
                                        <h3>Dennis Mugo <small>3.00 PM</small></h3>
                                        <p>Ut enim ad minim veniam, quis nostrud exercitation enim ad minim veniam, quis nostrud exercitation...</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#">
                                <div class="mail_list">
                                    <div class="left">
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="right">
                                        <h3>Jane Nobert <small>4.09 PM</small></h3>
                                        <p><span class="badge">To</span> Ut enim ad minim veniam, quis nostrud exercitation enim ad minim veniam, quis nostrud exercitation...</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#">
                                <div class="mail_list">
                                    <div class="left">
                                        <i class="fa fa-circle-o"></i><i class="fa fa-paperclip"></i>
                                    </div>
                                    <div class="right">
                                        <h3>Musimbi Anne <small>4.09 PM</small></h3>
                                        <p><span class="badge">CC</span> Ut enim ad minim veniam, quis nostrud exercitation enim ad minim veniam, quis nostrud exercitation...</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#">
                                <div class="mail_list">
                                    <div class="left">
                                        <i class="fa fa-paperclip"></i>
                                    </div>
                                    <div class="right">
                                        <h3>Jon Dibbs <small>4.09 PM</small></h3>
                                        <p>Ut enim ad minim veniam, quis nostrud exercitation enim ad minim veniam, quis nostrud exercitation...</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#">
                                <div class="mail_list">
                                    <div class="left">
                                        .
                                    </div>
                                    <div class="right">
                                        <h3>Debbis &amp; Raymond <small>4.09 PM</small></h3>
                                        <p>Ut enim ad minim veniam, quis nostrud exercitation enim ad minim veniam, quis nostrud exercitation...</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#">
                                <div class="mail_list">
                                    <div class="left">
                                        .
                                    </div>
                                    <div class="right">
                                        <h3>Debbis &amp; Raymond <small>4.09 PM</small></h3>
                                        <p>Ut enim ad minim veniam, quis nostrud exercitation enim ad minim veniam, quis nostrud exercitation...</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#">
                                <div class="mail_list">
                                    <div class="left">
                                        <i class="fa fa-circle"></i> <i class="fa fa-edit"></i>
                                    </div>
                                    <div class="right">
                                        <h3>Dennis Mugo <small>3.00 PM</small></h3>
                                        <p>Ut enim ad minim veniam, quis nostrud exercitation enim ad minim veniam, quis nostrud exercitation...</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#">
                                <div class="mail_list">
                                    <div class="left">
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="right">
                                        <h3>Jane Nobert <small>4.09 PM</small></h3>
                                        <p>Ut enim ad minim veniam, quis nostrud exercitation enim ad minim veniam, quis nostrud exercitation...</p>
                                    </div>
                                </div>
                            </a>
 */