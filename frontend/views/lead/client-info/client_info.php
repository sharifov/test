<?php

/**
 * @var $this View
 * @var $lead Lead
 * @var $leadForm LeadForm
 */

use common\models\ClientEmailQuery;
use common\models\Employee;
use common\models\Lead;
use common\models\query\ClientPhoneQuery;
use yii\helpers\Html;
use frontend\models\LeadForm;
use yii\web\View;
use yii\helpers\Url;
use yii\bootstrap4\Modal;
use src\auth\Auth;
use modules\lead\src\abac\LeadAbacObject;

/**
 * @var Employee $user
 * @var $unsubscribedEmails array
 * @var $leadAbacDto \stdClass
 * @var $unsubscribe bool
 * @var $disableMasking bool
 */
$user = Yii::$app->user->identity;

$formId = sprintf('%s-form', $leadForm->getClient()->formName());
//$manageClientInfoAccess = \src\access\ClientInfoAccess::isUserCanManageLeadClientInfo($lead, $user);

?>

    <div class="x_panel">
        <div class="x_title" >
            <h2><i class="fa fa-user-circle-o"></i> Client Info</h2>
            <?php yii\widgets\Pjax::begin(['id' => 'pjax-client-info', 'enablePushState' => false, 'enableReplaceState' => false]) ?>
            <ul class="nav navbar-right panel_toolbox">
                <?php /*if ($leadForm->mode !== $leadForm::VIEW_MODE || $manageClientInfoAccess) : */?>

                <?php //TODO: Remove /** @abac $leadAbacDto, LeadAbacObject::UI_MENU_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS, Access to Menu in Client Info block on lead */ ?>
                <?php //if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_MENU_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_DETAILS)) : ?>
                    <?php /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_DETAILS, Access to button client details on lead */ ?>
                    <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_DETAILS)) : ?>
                        <li>
                            <?=Html::a('<i class="fas fa-info-circle"></i> Details', '#', [
                                'id' => 'btn-client-info-details',
                                'data-lead-id' => $lead->id,
                                'data-client-id' => $leadForm->getClient()->id,
                                'title' => 'Client Info',
                            ])?>
                        </li>
                    <?php endif; ?>

                    <?php /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_ADD_PHONE, Access to button client add phone on lead*/ ?>
                    <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_ADD_PHONE)) : ?>
                        <li>
                            <?=Html::a('<i class="fas fa-plus-circle success"></i> Add Phone', '#', [
                                'id' => 'client-new-phone-button',
                                'data-modal_id' => 'client-manage-info',
                                'title' => 'Add Phone',
                                'data-content-url' => Url::to(['lead-view/ajax-add-client-phone-modal-content', 'gid' => $lead->gid]),
                                'class' => 'showModalButton'
                            ])?>
                        </li>
                    <?php endif; ?>

                    <?php /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_ADD_EMAIL, Access to button client add email on lead*/ ?>
                    <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_ADD_EMAIL)) : ?>
                        <li>
                            <?=Html::a('<i class="fas fa-plus-circle success"></i> Add Email', '#', [
                                'id' => 'client-new-email-button',
                                'data-modal_id' => 'client-manage-info',
                                'title' => 'Add Email',
                                'data-content-url' => Url::to(['lead-view/ajax-add-client-email-modal-content', 'gid' => $lead->gid]),
                                'class' => 'showModalButton'
                            ])?>
                        </li>
                    <?php endif; ?>

                    <?php /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_UPDATE_CLIENT, Access to button client add email on lead*/ ?>
                    <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_UPDATE_CLIENT)) : ?>
                        <li>
                            <?=Html::a('<i class="fas fa-edit warning"></i> Update Client', '#', [
                                'id' => 'client-edit-user-name-button',
                                'data-modal_id' => 'client-manage-info',
                                'title' => 'Update user name',
                                'data-content-url' => Url::to(['lead-view/ajax-edit-client-name-modal-content', 'gid' => $lead->gid]),
                                'class' => 'showModalButton'
                            ])?>
                        </li>
                    <?php endif; ?>

                    <?php if ($unsubscribe) : ?>
                        <?php /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_SUBSCRIBE, Access to button client subscribe on lead*/ ?>
                        <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_SUBSCRIBE)) : ?>
                            <li>
                                <?=Html::a('<i class="far fa-bell info"></i> Subscribe', '#', [
                                    'id' => 'client-subscribe-button',
                                    'title' => 'Allow communication with client',
                                    'data-subscribe-url' => Url::to(['client-project/subscribe-client-ajax',
                                        'clientId' => $lead->client_id,
                                        'projectId' => $lead->project_id,
                                        'leadId' => $lead->id,
                                        'action' => false
                                    ]),
                                ])?>
                            </li>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS, Access to button client unsubscribe on lead*/ ?>
                        <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_UNSUBSCRIBE)) : ?>
                            <li>
                                <?=Html::a('<i class="far fa-bell-slash info"></i> Unsubscribe', '#', [
                                    'id' => 'client-unsubscribe-button',
                                    'title' => 'Restrict communication with client',
                                    'data-unsubscribe-url' => Url::to(['client-project/unsubscribe-client-ajax',
                                        'clientId' => $lead->client_id,
                                        'projectId' => $lead->project_id,
                                        'leadId' => $lead->id,
                                        'action' => true
                                    ]),
                                ])?>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php //endif; ?>

                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <?php \yii\widgets\Pjax::end(); ?>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">

            <?php if ($lead->client->isExcluded()) : ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger" role="alert">
                        <b><i class="fa fa-warning"></i> Warning!</b> Excluded client.
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="" style="max-height: 150px; overflow-x: hidden;">
                <div class="row">
                    <div class="col-md-4">
                        <?= $this->render('_client_manage_name', [
                            'client' => $lead->client
                        ]) ?>
                    </div>

                    <div class="col-md-4">
                        <div id="client-manage-phone">
                            <?php if ($phones = ClientPhoneQuery::getWithSameClientsPhonesCount($lead->client_id)) : ?>
                                <?php
                                if ($leadForm->viewPermission) {
                                    echo $this->render('_client_manage_phone', [
                                        'clientPhones' => $phones,
                                        'lead' => $lead,
                                        'leadAbacDto' => $leadAbacDto,
                                        'disableMasking' => $disableMasking
                                    ]);
                                }
                                ?>
                            <?php endif; ?>
                        </div>
                        <div id="client-manage-email">
                            <?php if ($emails = ClientEmailQuery::getWithSameClientsEmailsCount($lead->client_id)) : ?>
                                <?php
                                if ($leadForm->viewPermission) {
                                    echo $this->render('_client_manage_email', [
                                        'clientEmails' => $emails,
                                        'lead' => $lead,
                                        //'manageClientInfoAccess' => $manageClientInfoAccess
                                        'leadAbacDto' => $leadAbacDto,
                                        'disableMasking' => $disableMasking
                                    ]);
                                }
                                ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <?= \frontend\widgets\client\ClientCounterWidget::widget([
                            'clientId' => $leadForm->getClient()->id,
                            'userId' => $user->id
                        ]) ?>

                        <?php /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_SHOW_LEADS_BY_IP, Access to btn search leads by ip on lead*/ ?>
                        <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_SHOW_LEADS_BY_IP)) : ?>
                            <?php if (!empty($leadForm->getLead()->request_ip)) : ?>
                                <?= $this->render('_client_ip_info', ['lead' => $leadForm->getLead()]) ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // add email comment button
        function addEmailComment(element, key) {
            $('#preloader').removeClass('hidden');
            var form = element.parent();
            $.post(form.attr('action'), form.serialize(), function (data) {
                //location.reload();
                $('#addEmailComment-' + key).trigger('click');
                $('#preloader').addClass('hidden');
            });
        }
        // add email comment button
        function addPhoneComment(element, key) {
            $('#preloader').removeClass('hidden');
            var form = element.parent();
            $.post(form.attr('action'), form.serialize(), function (data) {
                //location.reload();
                $('#addPhoneComment-' + key).trigger('click');
                $('#preloader').addClass('hidden');
            });
        }
    </script>


<?= Modal::widget([
    'title' => '',
    'id' => 'modal-client-manage-info',
    'size' => Modal::SIZE_SMALL,
]) ?>

<?= Modal::widget([
    'title' => '',
    'id' => 'modal-client-large',
    'size' => Modal::SIZE_LARGE,
])
?>
<?php
$clientInfoUrl = \yii\helpers\Url::to(['/lead-view/ajax-get-info']);

$js = <<<JS
    $(document).on('click', '#btn-client-info-details', function(e) {
        e.preventDefault();
        var client_id = $(this).data('client-id');
        var lead_id = $(this).data('lead-id');
        $('#modalLead .modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        $('#modalLead-label').html('Client Details (' + client_id + ')');
        $('#modalLead').modal();
        $.post('$clientInfoUrl', {client_id: client_id, lead_id: lead_id},
            function (data) {
                $('#modalLead .modal-body').html(data);
            }
        );
    });
JS;
$this->registerJs($js);
?>