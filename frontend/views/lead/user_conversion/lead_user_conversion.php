<?php

use common\components\i18n\Formatter;
use common\models\Lead;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/* @var View $this */
/* @var Lead $lead */
/* @var LeadAbacDto $leadAbacDto */
?>

<?php Pjax::begin(['id' => 'pjax-user-conversation-list']); ?>

    <div class="x_panel" id="user-conversation-list">
        <div class="x_title">
            <h2>
                 User Conversion
<?php /** @abac $leadAbacDto, LeadAbacObject::ACT_USER_CONVERSION, LeadAbacObject::ACTION_CREATE, Create User Conversation */ ?>
<?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_USER_CONVERSION, LeadAbacObject::ACTION_CREATE)) : ?>
<small>
    <?php echo Html::a(
        '<i class="fa fa-plus"></i>',
        null,
        [
            'class' => 'js-add-conversation-btn',
            'title' => 'Add',
            'id' => 'js-add-conversation-btn',
        ]
    );
    ?>
</small>
<?php endif ?>
            </h2>
            <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: block;">
        <?php if ($lead->leadUserConversion) : ?>
            <?php foreach ($lead->leadUserConversion as $leadUserConversion) : ?>
                <div id="conversion-box-<?php echo $leadUserConversion->luc_user_id ?>-<?php echo $leadUserConversion->luc_lead_id ?>">
                    <i class="fa fa-user"></i> <?php echo $leadUserConversion->lucUser->username ?>&nbsp;
                    <i class="fa fa-calendar" title="<?php echo (new Formatter())->asDatetime($leadUserConversion->luc_created_dt, 'php:d-M-Y [H:i]') ?>"></i>&nbsp;

                    <?php if ($leadUserConversion->luc_description) : ?>
                        <i class="fa fa-commenting" title="<?php echo Html::encode($leadUserConversion->luc_description) ?>"></i>&nbsp;
                    <?php endif ?>

                    <?php /** @abac $leadAbacDto, LeadAbacObject::ACT_USER_CONVERSION, LeadAbacObject::ACTION_DELETE, Delete User Conversation */ ?>
                    <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_USER_CONVERSION, LeadAbacObject::ACTION_DELETE)) : ?>
                        <?php echo Html::a(
                            '<i class="fa fa-times"></i>',
                            null,
                            [
                                'class' => 'js-remove-conversation-btn text-danger',
                                'title' => 'Delete',
                                'data-lead-id' => $leadUserConversion->luc_lead_id,
                                'data-user-id' => $leadUserConversion->luc_user_id,
                            ]
                        );
                        ?>
                    <?php endif; ?>
                    &nbsp;&nbsp;
                </div>
            <?php endforeach ?>
        <?php endif; ?>
        </div>
    </div>

<?php
    Pjax::end();
