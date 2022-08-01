<?php

use yii\helpers\Html;
use src\entities\email\helpers\EmailType;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model src\entities\email\Email */
/* @var $selectedId int */
?>

<?php if ($selectedId == $model->e_id) :?>
<div style="padding: 8px; background-color: rgba(175,255,236,0.5); color: darkgreen">
<?php else : ?>
<a style="color: <?= ($model->isDeleted() ? 'darkred' : ($model->isNew() ? 'blue' : 'black'))?>" href="<?= Url::current(['id' => $model->e_id, 'action' => null])?>" class="view_email" data-email-id="<?= $model->e_id?>">
<?php endif; ?>

    <div class="mail_list">
        <div class="left">
            <?php if (EmailType::isDraft($model->e_type_id)) :?>
                <i class="fa fa-edit" title="Draft"></i><br>
            <?php  elseif (EmailType::isOutbox($model->e_type_id)) :?>
                <i class="fa fa-arrow-circle-up" title="Outbox"></i><br>
            <?php elseif (EmailType::isInbox($model->e_type_id)) :?>
                <i class="fa fa-arrow-circle-down" title="Inbox"></i><br>
            <?php endif; ?>

            <?php if ($model->isDeleted()) :?>
                <i class="fa fa-trash" title="Trash"></i><br>
            <?php endif; ?>

            <?php if ($model->isNew()) :?>
                <i class="fa fa-circle" title="New message"></i><br>
            <?php endif; ?>
        </div>
        <div class="right">
            <h3>
                <?= Html::encode($model->emailFrom)?> - <?= Html::encode($model->emailTo)?>
                <small>
                    <?php if (EmailType::isInbox($model->e_type_id)) : ?>
                        <?= $model->emailLog->el_inbox_email_id ? 'cid: ' . $model->emailLog->el_inbox_email_id : ''?><br/>
                        <?= $model->emailLog->el_inbox_created_dt ? Yii::$app->formatter->asDatetime(strtotime($model->emailLog->el_inbox_created_dt)) : '-'?>
                    <?php elseif (EmailType::isOutbox($model->e_type_id)) : ?>
                        <?= Yii::$app->formatter->asDatetime(strtotime($model->e_created_dt))?>
                    <?php elseif (EmailType::isDraft($model->e_type_id)) : ?>
                        <i><?= Yii::$app->formatter->asDatetime(strtotime($model->e_created_dt))?></i>
                    <?php endif; ?>

                </small>
            </h3>
            <p><?php if ($model->project) :?>
                <span class="label label-info"><?= Html::encode($model->project->name)?></span>
               <?php endif;?><?= Html::encode($model->emailSubject)?></p>
        </div>
    </div>
<?php if ($selectedId == $model->e_id) :?>
</div>
<?php else : ?>
</a>
<?php endif; ?>