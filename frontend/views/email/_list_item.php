<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Email */
/* @var $modelEmailView common\models\Email */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>

<?php if($modelEmailView && $modelEmailView->e_id == $model->e_id):?>
<div style="color: darkgreen">
<? else: ?>

<a style="color: <?=($model->e_is_deleted ? 'darkred' : ($model->e_is_new ? 'blue' : 'black'))?>" href="<?=\yii\helpers\Url::current(['id' => $model->e_id, 'action' => null, 'edit_id' => null, 'reply_id' => null ])?>" class="view_email" data-email-id="<?=$model->e_id?>">
<? endif; ?>

    <div class="mail_list">
        <div class="left">
            <?php if($model->e_is_new):?>
                <i class="fa fa-circle"></i>
            <? endif; ?><?/*<i class="fa fa-edit"></i>*/?>
        </div>
        <div class="right">
            <h3>
                <?=Html::encode($model->e_email_from)?> - <?=Html::encode($model->e_email_to)?> <small><?=Yii::$app->formatter->asDatetime(strtotime($model->e_created_dt))?></small></h3>
            <p><?php if($model->eProject):?>
                <span class="label label-info"><?=Html::encode($model->eProject->name)?></span>
                <?php endif;?><?=Html::encode($model->e_email_subject)?></p>
            <?/*php echo (\yii\helpers\StringHelper::truncate(\common\models\Email::strip_html_tags($model->e_email_body_html), 150, '...', null, true))*/?>
        </div>
    </div>
<?php if($modelEmailView && $modelEmailView->e_id == $model->e_id):?>
    </div>

<? else: ?>
</a>
<? endif; ?>

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