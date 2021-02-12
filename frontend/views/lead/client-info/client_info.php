<?php

/**
 * @var $this View
 * @var $lead Lead
 * @var $leadForm LeadForm
 */

use common\models\Employee;
use common\models\Lead;
use yii\helpers\Html;
use frontend\models\LeadForm;
use yii\web\View;
use yii\helpers\Url;
use yii\bootstrap4\Modal;
use sales\auth\Auth;

/**
 * @var Employee $user
 * @var $unsubscribedEmails array
 */
$user = Yii::$app->user->identity;

$formId = sprintf('%s-form', $leadForm->getClient()->formName());
$manageClientInfoAccess = \sales\access\ClientInfoAccess::isUserCanManageLeadClientInfo($lead, $user);

?>

    <div class="x_panel">
        <div class="x_title" >
            <h2><i class="fa fa-user-circle-o"></i> Client Info</h2>
            <?php yii\widgets\Pjax::begin(['id' => 'pjax-client-info', 'enablePushState' => false, 'enableReplaceState' => false]) ?>
            <ul class="nav navbar-right panel_toolbox">
                <?php if ($leadForm->mode !== $leadForm::VIEW_MODE || $manageClientInfoAccess) : ?>
                    <li>
                        <?=Html::a('<i class="fas fa-info-circle"></i> Details', '#', [
                            'id' => 'btn-client-info-details',
                            'data-client-id' => $leadForm->getClient()->id,
                            'title' => 'Client Info',
                        ])?>
                    </li>
                    <li>
                        <?=Html::a('<i class="fas fa-plus-circle success"></i> Add Phone', '#', [
                            'id' => 'client-new-phone-button',
                            'data-modal_id' => 'client-manage-info',
                            'title' => 'Add Phone',
                            'data-content-url' => Url::to(['lead-view/ajax-add-client-phone-modal-content', 'gid' => $lead->gid]),
                            'class' => 'showModalButton'
                        ])?>
                    </li>
                    <li>
                        <?=Html::a('<i class="fas fa-plus-circle success"></i> Add Email', '#', [
                            'id' => 'client-new-email-button',
                            'data-modal_id' => 'client-manage-info',
                            'title' => 'Add Email',
                            'data-content-url' => Url::to(['lead-view/ajax-add-client-email-modal-content', 'gid' => $lead->gid]),
                            'class' => 'showModalButton'
                        ])?>
                    </li>
                    <li>
                        <?=Html::a('<i class="fas fa-edit warning"></i> Update Client', '#', [
                            'id' => 'client-edit-user-name-button',
                            'data-modal_id' => 'client-manage-info',
                            'title' => 'Update user name',
                            'data-content-url' => Url::to(['lead-view/ajax-edit-client-name-modal-content', 'gid' => $lead->gid]),
                            'class' => 'showModalButton'
                        ])?>
                    </li>

                    <?php if ($unsubscribe) : ?>
                        <?php if (Auth::can('client-project/subscribe-client-ajax')) : ?>
                            <li>
                                <?=Html::a('<i class="far fa-bell-slash info"></i> Subscribe', '#', [
                                    'id' => 'client-unsubscribe-button',
                                    'title' => 'Allow communication with client',
                                    'data-unsubscribe-url' => Url::to(['client-project/unsubscribe-client-ajax',
                                        'clientID' => $lead->client_id,
                                        'projectID' => $lead->project_id,
                                        'action' => false
                                    ]),
                                ])?>
                            </li>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php if (Auth::can('client-project/unsubscribe-client-ajax')) : ?>
                            <li>
                                <?=Html::a('<i class="far fa-bell-slash info"></i> Unsubscribe', '#', [
                                    'id' => 'client-unsubscribe-button',
                                    'title' => 'Restrict communication with client',
                                    'data-unsubscribe-url' => Url::to(['client-project/unsubscribe-client-ajax',
                                        'clientID' => $lead->client_id,
                                        'projectID' => $lead->project_id,
                                        'action' => true
                                    ]),
                                ])?>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>

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
                            <?php if ($phones = $lead->client->clientPhones) : ?>
                                <?php
                                if ($leadForm->viewPermission) {
                                    echo $this->render('_client_manage_phone', [
                                        'clientPhones' => $phones,
                                        'lead' => $lead,
                                        'manageClientInfoAccess' => $manageClientInfoAccess
                                    ]);
                                }
                                ?>
                            <?php endif; ?>
                        </div>
                        <div id="client-manage-email">
                            <?php if ($emails = $lead->client->clientEmails) : ?>
                                <?php
                                if ($leadForm->viewPermission) {
                                    echo $this->render('_client_manage_email', [
                                        'clientEmails' => $emails,
                                        'lead' => $lead,
                                        'manageClientInfoAccess' => $manageClientInfoAccess
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

                        <?php if (!empty($leadForm->getLead()->request_ip)) : ?>
                            <?= $this->render('_client_ip_info', ['lead' => $leadForm->getLead()]) ?>
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
$clientInfoUrl = \yii\helpers\Url::to(['/client/ajax-get-info']);

$js = <<<JS
    $(document).on('click', '#btn-client-info-details', function(e) {
        e.preventDefault();
        var client_id = $(this).data('client-id');
        $('#modalLead .modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        $('#modalLead-label').html('Client Details (' + client_id + ')');
        $('#modalLead').modal();
        $.post('$clientInfoUrl', {client_id: client_id},
            function (data) {
                $('#modalLead .modal-body').html(data);
            }
        );
    });
JS;
$this->registerJs($js);
?>