<?php

use common\components\i18n\Formatter;
use common\models\Lead;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var View $this */
/* @var Lead $lead */
/* @var LeadAbacDto $leadAbacDto */
?>

<?php if ($lead->leadUserConversion) : ?>
    <div class="x_panel" id="user-conversation-list">
        <div class="x_title">
            <h2><i class="fa fa-user"></i> User Conversion</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: block;">
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
        </div>
    </div>

    <?php
    $removeConversationUrl = Url::to(['/lead-user-conversion/delete']);

    $js = <<<JS
    $(document).on('click', '.js-remove-conversation-btn', function (e) {
        e.preventDefault();
    
        if(!confirm('Are you sure you want to delete this item?')) {
            return false;
        }
    
        let leadId = $(this).attr('data-lead-id');
        let userId = $(this).attr('data-user-id');
        let btnSubmit = $(this);
        let btnContent = btnSubmit.html();
    
        btnSubmit.html('<i class="fa fa-cog fa-spin"></i> Loading...').prop('disabled', true);
    
        $.ajax({
            url: '{$removeConversationUrl}',
            type: 'POST',
            data: {lead_id: leadId, user_id: userId},
            dataType: 'json'
        })
        .done(function(dataResponse) {
            if (dataResponse.status > 0) {
                createNotify('Success', dataResponse.message, 'success');
                
                $('#conversion-box-' + userId + '-' + leadId).remove();
            } else if (dataResponse.message.length) {
                createNotify('Error', dataResponse.message, 'error');
            } else {
                createNotify('Error', 'Error, please check logs', 'error');
            }
            btnSubmit.html(btnContent).removeClass('btn-default').prop('disabled', false);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            createNotify('Error', jqXHR.responseText, 'error');
            btnSubmit.html(btnContent).removeClass('btn-default').prop('disabled', false);
        })
        .always(function(jqXHR, textStatus, errorThrown) {
            setTimeout(function () {
                btnSubmit.html(btnContent).removeClass('btn-default').prop('disabled', false);
            }, 5000);
        });
    });
JS;
    $this->registerJs($js);
endif;
?>
